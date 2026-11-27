<?php

namespace App\Modules\EmployeeCompensation\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Modules\EmployeeCompensation\Repositories\Contracts\EmployeeCompensationContract;
use App\Modules\EmployeeCompensation\Http\Requests\EmployeeCompensationRequest;
use App\Modules\EmployeeCompensation\Models\EmployeeCompensation;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Status;
use App\Enum\PayTypeEnum;
use App\Modules\Employee\Models\Employee;
use App\Modules\Designation\Models\Designation;

class EmployeeCompensationController extends BaseModuleController
{
    protected $status;

    public function __construct(
        protected EmployeeCompensationContract $employeeCompensationRepo
    ){
        $this->status = new Status();
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'employee_name' => ['label' => 'Employee', 'html' => true, 'orderable' => false, 'searchable' => 'employee.name'],
            'joining_date' => ['label' => 'Joining Date', 'html' => true, 'orderable' => false, 'searchable' => false],
            'designation_name' => ['label' => 'Designation', 'html' => true, 'orderable' => false, 'searchable' => 'designation.name'],
            'pay_type_label' => ['label' => 'Salary Type', 'html' => true, 'orderable' => false, 'searchable' => false],
            'salary_display' => ['label' => 'Salary / Rate', 'html' => true, 'orderable' => false, 'searchable' => false],
            'effective_from' => ['label' => 'Effective From', 'html' => true, 'orderable' => false, 'searchable' => false],
            'status_label' => ['label' => 'Status', 'html' => true, 'orderable' => false, 'searchable' => false],
            'author_display' => ['label' => 'Author', 'html' => true, 'orderable' => false, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action_html' => ['label' => 'Action', 'html' => true, 'orderable' => false, 'searchable' => false],
        ];

        $query = $this->employeeCompensationRepo->getAll()
            ->with(['employee', 'designation', 'status', 'author']);
        $total_count = $query->count();

        $dataTable = new \App\Services\DataTableService(
            model: $query,
            columns: $columns,
            rowFormatter: [$this, 'formatRow']
        );

        if ($request->ajax() && $request->loaddata == "yes") {
            return $dataTable->ajax();
        }

        return view(strtolower($this->pathInitialize.'.index'), $this->viewWithVars(get_defined_vars()));
    }

    public function formatRow($row)
    {
        $row->employee_name = e($row->employee?->name ?? '-');
        $row->joining_date = $row->employee?->joining_date
            ? \Illuminate\Support\Carbon::parse($row->employee->joining_date)->format('d M Y')
            : '-';
        $row->designation_name = e($row->designation?->name ?? '-');
        $row->pay_type_label = PayTypeEnum::tryFrom((string) $row->pay_type)?->label() ?? ucfirst((string) $row->pay_type);
        [$salary, $unit] = match ($row->pay_type) {
            'monthly', 'hybrid' => [$row->basic_pay, ' / month'],
            'daily' => [$row->daily_rate, ' / day'],
            'hourly' => [$row->hourly_rate, ' / hour'],
            'commission' => [$row->commission_rate, ' commission'],
            default => [null, ''],
        };
        $row->salary_display = $salary !== null
            ? 'Rs. '.number_format((float) $salary, 2).$unit
            : '-';
        $row->effective_from = $row->effective_from
            ? \Illuminate\Support\Carbon::parse($row->effective_from)->format('d M Y')
            : '-';

        $status = $row->status?->name ?? 'de-active';
        $row->status_label = '<span class="badge rounded-pill px-3 py-2 '. badgeClass($status) .'">'
                    . strtoupper($status) .
                    '</span>';

        $row->author_display = $row->author
                ? view('back-office.partials.avatar', ['user' => $row->author])->render()
                : '-';

        $row->action_html = view('back-office.partials.actions', [
            'model'            => $row,
            'permissionPrefix' => $this->permissionPrefix,
            'routeInitialize'  => $this->routePrefix,
            'singularLabel'    => $this->singularLabel,
        ])->render();

        return $row;
    }

    public function create()
    {
        $employees = Employee::query()
            ->where('status_id', statusId('Employee', 'active'))
            ->orderBy('name')
            ->get();
        $designations = Designation::query()
            ->where('status_id', statusId('Designation', 'active'))
            ->orderBy('name')
            ->get();
        $payTypes = $this->supportedPayTypes();
        $commissionBasises = \App\Enum\CommissionBasisEnum::cases();
        $commissionTypes = \App\Enum\CommissionTypeEnum::cases();

        return (string) view($this->pathInitialize.'.create_content', get_defined_vars());
    }

    public function store(EmployeeCompensationRequest $request)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $response = $this->employeeCompensationRepo->storeModel($payload);
            });
            return successResponse($response, module_message('created', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(EmployeeCompensation $employeeCompensation)
    {
        $employees = Employee::query()->orderBy('name')->get();
        $designations = Designation::query()->where('status_id', statusId('Designation', 'active'))->orderBy('name')->get();
        $payTypes = $this->supportedPayTypes();
        $commissionBasises = \App\Enum\CommissionBasisEnum::cases();
        $commissionTypes = \App\Enum\CommissionTypeEnum::cases();
        $statuses = $this->status->where('model', 'EmployeeCompensation')->get();
        $model = $this->employeeCompensationRepo->showModel($employeeCompensation, ['employee', 'designation']);
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(EmployeeCompensationRequest $request, EmployeeCompensation $employeeCompensation)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $employeeCompensation) {
                $response = $this->employeeCompensationRepo->updateModel($employeeCompensation, $payload);
            });
            return successResponse($response, module_message('updated', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(EmployeeCompensation $employeeCompensation)
    {
        $model = $this->employeeCompensationRepo->showModel($employeeCompensation, ['employee', 'designation', 'status']);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(EmployeeCompensation $employeeCompensation)
    {
        try {
            if($this->employeeCompensationRepo->softDeleteModel($employeeCompensation)) {
                return response()->json([
                    'status' => true,
                    'message' => module_message('deleted', $this->singularLabel)
                ]);
            } else{
                return response()->json([
                    'status' => false,
                    'error' => $this->singularLabel.' not deleted try again.'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function restore(EmployeeCompensation $employeeCompensation)
    {
        try {
            if($this->employeeCompensationRepo->restoreModel($employeeCompensation)) {
                return redirect()->back()->with('message', module_message('restored', $this->singularLabel));
            } else {
                return false;
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function forceDelete(EmployeeCompensation $employeeCompensation)
    {
        try {
            if ($this->employeeCompensationRepo->permanentlyDeleteModel($employeeCompensation)) {
                return response()->json([
                    'status' => true,
                    'message' => module_message('permanently-deleted', $this->singularLabel)
                ]);
            } else{
                return response()->json([
                    'status' => true,
                    'error' => $this->singularLabel.' not deleted try again.'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function bulkDelete()
    {
        try {
            $this->employeeCompensationRepo->bulkDelete();
            return redirect()->route('back-office.employee-compensations.index')->with('success', value: module_message('bulk-deleted', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->employeeCompensationRepo->bulkRestore();
            return redirect()->route('back-office.employee-compensations.index')->with('success', module_message('bulk-restored', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function supportedPayTypes(): array
    {
        return array_values(array_filter(
            PayTypeEnum::cases(),
            fn (PayTypeEnum $type) => in_array($type->value, ['monthly', 'daily', 'hourly'], true)
        ));
    }
}
