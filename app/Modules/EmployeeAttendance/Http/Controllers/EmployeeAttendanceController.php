<?php

namespace App\Modules\EmployeeAttendance\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Modules\EmployeeAttendance\Repositories\Contracts\EmployeeAttendanceContract;
use App\Modules\EmployeeAttendance\Http\Requests\EmployeeAttendanceRequest;
use App\Modules\EmployeeAttendance\Models\EmployeeAttendance;
use App\Modules\Employee\Models\Employee;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Status;

class EmployeeAttendanceController extends BaseModuleController
{
    protected $status;
    protected $employeeModel;

    public function __construct(
        protected EmployeeAttendanceContract $employeeAttendanceRepo
    ){
        $this->status = new Status();
        $this->employeeModel = new Employee();
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'employee_name'      => ['label' => 'Employee', 'html' => true, 'searchable' => false],
            'status_name'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'author_id'     => ['label' => 'Author', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        $query = $this->employeeAttendanceRepo->getAll();
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
        $status = $row->status?->name;
        $row->status_name = '<span class="badge rounded-pill px-3 py-2 '. badgeClass($status) .'">'
                            . strtoupper($status) .
                            '</span>';

        $row->author_id = $row->author
                ? view('back-office.partials.avatar', ['user' => $row->author])->render()
                : '-';
        $row->employee_name = $row->employee
                ? view('back-office.partials.avatar', ['user' => $row->employee])->render()
                : '-';

        $row->action = view('back-office.partials.actions', [
            'model'            => $row,
            'permissionPrefix' => $this->permissionPrefix,
            'routeInitialize'  => $this->routePrefix,
            'singularLabel'    => $this->singularLabel,
        ])->render();

        return $row;
    }

    public function create()
    {
        $formData = $this->getFormData();

        return (string) view(
            $this->pathInitialize.'.create_content',
            array_merge($formData, get_defined_vars())
        );
    }

    public function store(EmployeeAttendanceRequest $request)
    {
        $payload = $request->validated();

        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $this->employeeAttendanceRepo->storeModel($payload);
            });
            return successResponse($response, module_message('created', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(EmployeeAttendance $employeeAttendance)
    {
        $formData = $this->getFormData();
        $model = $this->employeeAttendanceRepo->showModel($employeeAttendance);

        return (string) view(
            $this->pathInitialize.'.edit_content',
            array_merge($formData, get_defined_vars())
        );
    }

    public function update(EmployeeAttendanceRequest $request, EmployeeAttendance $employeeAttendance)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $employeeAttendance) {
                $this->employeeAttendanceRepo->updateModel($employeeAttendance, $payload);
            });
            return successResponse($response, module_message('updated', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(EmployeeAttendance $employeeAttendance)
    {
        $model = $this->employeeAttendanceRepo->showModel($employeeAttendance);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(EmployeeAttendance $employeeAttendance)
    {
        try {
            if($this->employeeAttendanceRepo->softDeleteModel($employeeAttendance)) {
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

    public function restore(EmployeeAttendance $employeeAttendance)
    {
        try {
            if($this->employeeAttendanceRepo->restoreModel($employeeAttendance)) {
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

    public function forceDelete(EmployeeAttendance $employeeAttendance)
    {
        try {
            if ($this->employeeAttendanceRepo->permanentlyDeleteModel($employeeAttendance)) {
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
            $this->employeeAttendanceRepo->bulkDelete();
            return redirect()->route('back-office.employee_attendances.index')->with('success', value: module_message('bulk-deleted', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->employeeAttendanceRepo->bulkRestore();
            return redirect()->route('back-office.employee_attendances.index')->with('success', module_message('bulk-restored', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function getFormData(): array
    {
        return [
            'employees' => $this->employeeModel->where('status_id', statusId('Employee', 'active'))->get(),
            'statuses' => $this->status->where('model', 'EmployeeAttandence')->get(),
        ];
    }
}
