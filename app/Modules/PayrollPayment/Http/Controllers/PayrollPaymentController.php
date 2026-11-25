<?php

namespace App\Modules\PayrollPayment\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Modules\PayrollPayment\Repositories\Contracts\PayrollPaymentContract;
use App\Modules\PayrollPayment\Http\Requests\PayrollPaymentRequest;
use App\Modules\PayrollPayment\Models\PayrollPayment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Status;

class PayrollPaymentController extends BaseModuleController
{
    protected $status;

    public function __construct(
        protected PayrollPaymentContract $payrollPaymentRepo
    ){
        $this->status = new Status();
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'name'      => ['label' => 'name', 'searchable' => 'name'],
            'status'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'author_id'     => ['label' => 'Author', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        $query = $this->payrollPaymentRepo->getAll();
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
        $status = $row->status?->name ?? 'de-active';
        $row->status = '<span class="badge rounded-pill px-3 py-2 '. badgeClass($status) .'">'
                    . strtoupper($status) .
                    '</span>';

        $row->author_id = $row->author
                ? view('back-office.partials.avatar', ['user' => $row->author])->render()
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
        return (string) view($this->pathInitialize.'.create_content', get_defined_vars());
    }

    public function store(PayrollPaymentRequest $request)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $this->payrollPaymentRepo->storeModel($payload);
            });
            return successResponse($response, module_message('created', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(PayrollPayment $payrollPayment)
    {
        $statuses = $this->status->where('model', 'PayrollPayment')->get();
        $model = $this->payrollPaymentRepo->showModel($payrollPayment);
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(PayrollPaymentRequest $request, PayrollPayment $payrollPayment)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $payrollPayment) {
                $this->payrollPaymentRepo->updateModel($payrollPayment, $payload);
            });
            return successResponse($response, module_message('updated', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(PayrollPayment $payrollPayment)
    {
        $model = $this->payrollPaymentRepo->showModel($payrollPayment);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(PayrollPayment $payrollPayment)
    {
        try {
            if($this->payrollPaymentRepo->softDeleteModel($payrollPayment)) {
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

    public function restore(PayrollPayment $payrollPayment)
    {
        try {
            if($this->payrollPaymentRepo->restoreModel($payrollPayment)) {
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

    public function forceDelete(PayrollPayment $payrollPayment)
    {
        try {
            if ($this->payrollPaymentRepo->permanentlyDeleteModel($payrollPayment)) {
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
            $this->payrollPaymentRepo->bulkDelete();
            return redirect()->route('back-office.payroll_payments.index')->with('success', value: module_message('bulk-deleted', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->payrollPaymentRepo->bulkRestore();
            return redirect()->route('back-office.payroll_payments.index')->with('success', module_message('bulk-restored', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}