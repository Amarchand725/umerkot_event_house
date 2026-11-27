<?php

namespace App\Modules\Payroll\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Modules\Payroll\Repositories\Contracts\PayrollContract;
use App\Modules\Payroll\Http\Requests\PayrollRequest;
use App\Modules\Payroll\Models\Payroll;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Status;
use App\Modules\EmployeeCompensation\Models\EmployeeCompensation;

class PayrollController extends BaseModuleController
{
    protected $status;

    public function __construct(
        protected PayrollContract $payrollRepo
    ){
        $this->status = new Status();
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'employee_label' => ['label' => 'Employee', 'html' => true, 'orderable' => false, 'searchable' => 'employee.name'],
            'period_label' => ['label' => 'Period', 'html' => true, 'orderable' => false, 'searchable' => false],
            'net_payable_label' => ['label' => 'Net Payable', 'html' => true, 'orderable' => false, 'searchable' => false],
            'status_label' => ['label' => 'Status', 'html' => true, 'orderable' => false, 'searchable' => false],
            'author_display' => ['label' => 'Author', 'html' => true, 'orderable' => false, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action_html' => ['label' => 'Action', 'html' => true, 'orderable' => false, 'searchable' => false],
        ];

        $query = $this->payrollRepo->getAll()->with([
            'employee', 'employeeCompensation.designation', 'status', 'author',
        ]);
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
        $employeeName = $row->employee?->name ?? '-';
        $designation = $row->employeeCompensation?->designation?->name;
        $row->employee_label = e($employeeName).($designation ? ' ('.e($designation).')' : '');
        $row->period_label = \Illuminate\Support\Carbon::create(
            (int) $row->year,
            (int) $row->month,
            1
        )->format('F Y');
        $row->net_payable_label = 'Rs. '.number_format((float) $row->net_payable, 2);
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
        $compensations = EmployeeCompensation::query()
            ->with(['employee', 'designation'])
            ->where('status_id', statusId('EmployeeCompensation', 'active'))
            ->whereIn('pay_type', ['monthly', 'daily', 'hourly'])
            ->orderByDesc('effective_from')
            ->get();

        return (string) view($this->pathInitialize.'.create_content', get_defined_vars());
    }

    public function store(PayrollRequest $request)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $response = $this->payrollRepo->storeModel($payload);
            });
            return successResponse($response, module_message('created', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(Payroll $payroll)
    {
        $compensations = EmployeeCompensation::query()
            ->with(['employee', 'designation'])
            ->where('status_id', statusId('EmployeeCompensation', 'active'))
            ->whereIn('pay_type', ['monthly', 'daily', 'hourly'])
            ->orderByDesc('effective_from')
            ->get();
        $model = $this->payrollRepo->showModel($payroll, ['employee', 'employeeCompensation']);
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(PayrollRequest $request, Payroll $payroll)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $payroll) {
                $response = $this->payrollRepo->updateModel($payroll, $payload);
            });
            return successResponse($response, module_message('updated', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(Payroll $payroll)
    {
        $model = $this->payrollRepo->showModel($payroll, [
            'employee', 'employeeCompensation.designation', 'items.source', 'payments.status', 'payments.paymentMethod',
        ]);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(Payroll $payroll)
    {
        try {
            if($this->payrollRepo->softDeleteModel($payroll)) {
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

    public function restore(Payroll $payroll)
    {
        try {
            if($this->payrollRepo->restoreModel($payroll)) {
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

    public function forceDelete(Payroll $payroll)
    {
        try {
            if ($this->payrollRepo->permanentlyDeleteModel($payroll)) {
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
            $this->payrollRepo->bulkDelete();
            return redirect()->route('back-office.payrolls.index')->with('success', value: module_message('bulk-deleted', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->payrollRepo->bulkRestore();
            return redirect()->route('back-office.payrolls.index')->with('success', module_message('bulk-restored', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
