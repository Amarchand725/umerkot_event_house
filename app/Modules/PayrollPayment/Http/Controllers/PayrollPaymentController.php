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
use App\Modules\Payroll\Models\Payroll;
use App\Modules\PaymentMethod\Models\PaymentMethod;

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
            'employee_label' => ['label' => 'Employee', 'html' => true, 'orderable' => false, 'searchable' => false],
            'period_label' => ['label' => 'Payroll Period', 'html' => true, 'orderable' => false, 'searchable' => false],
            'amount_label' => ['label' => 'Amount', 'html' => true, 'orderable' => false, 'searchable' => false],
            'payment_date_label' => ['label' => 'Payment Date', 'html' => true, 'orderable' => false, 'searchable' => false],
            'payment_method_label' => ['label' => 'Payment Method', 'html' => true, 'orderable' => false, 'searchable' => false],
            'status_label' => ['label' => 'Status', 'html' => true, 'orderable' => false, 'searchable' => false],
            'author_display' => ['label' => 'Author', 'html' => true, 'orderable' => false, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action_html' => ['label' => 'Action', 'html' => true, 'orderable' => false, 'searchable' => false],
        ];

        $query = $this->payrollPaymentRepo->getAll()->with([
            'payroll.employee', 'paymentMethod', 'status', 'author',
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
        $employeeName = $row->payroll?->employee?->name ?? '-';
        $row->employee_label = e($employeeName);
        $row->period_label = $row->payroll
            ? \Illuminate\Support\Carbon::create((int) $row->payroll->year, (int) $row->payroll->month, 1)->format('F Y')
            : '-';
        $row->payment_method_label = e($row->paymentMethod?->name ?? '-');
        $row->payment_date_label = $row->payment_date
            ? \Illuminate\Support\Carbon::parse($row->payment_date)->format('d M Y')
            : '-';
        $row->amount_label = 'Rs. '.number_format((float) $row->amount, 2);
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
        $formData = $this->getFormData();
        return (string) view($this->pathInitialize.'.create_content', array_merge($formData, get_defined_vars()));
    }

    public function store(PayrollPaymentRequest $request)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $response = $this->payrollPaymentRepo->storeModel($payload);
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
        $formData = $this->getFormData($payrollPayment);
        $model = $this->payrollPaymentRepo->showModel($payrollPayment, ['payroll.employee', 'paymentMethod', 'status']);
        return (string) view($this->pathInitialize.'.edit_content', array_merge($formData, get_defined_vars()));
    }

    public function update(PayrollPaymentRequest $request, PayrollPayment $payrollPayment)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $payrollPayment) {
                $response = $this->payrollPaymentRepo->updateModel($payrollPayment, $payload);
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
        $model = $this->payrollPaymentRepo->showModel($payrollPayment, ['payroll.employee', 'paymentMethod', 'status', 'author']);
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
            return redirect()->route('back-office.payroll-payments.index')->with('success', value: module_message('bulk-deleted', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->payrollPaymentRepo->bulkRestore();
            return redirect()->route('back-office.payroll-payments.index')->with('success', module_message('bulk-restored', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function getFormData(?PayrollPayment $payment = null): array
    {
        $activePaymentStatusId = statusId('PayrollPayment', 'active');
        $payrollsQuery = Payroll::query()
            ->with('employee')
            ->withSum(['payments' => function ($query) use ($activePaymentStatusId) {
                if ($activePaymentStatusId) {
                    $query->where(function ($activeQuery) use ($activePaymentStatusId) {
                        $activeQuery->where('status_id', $activePaymentStatusId)
                            ->orWhereNull('status_id');
                    });
                }
            }], 'amount')
            ->orderByDesc('year')
            ->orderByDesc('month');
        $cancelledStatusId = statusId('Payroll', 'cancelled');
        if ($cancelledStatusId) {
            $payrollsQuery->where('status_id', '!=', $cancelledStatusId);
        }
        $payrolls = $payrollsQuery->get()
            ->filter(function (Payroll $payroll) use ($payment) {
                $remaining = (float) $payroll->net_payable - (float) ($payroll->payments_sum_amount ?? 0);
                return $remaining > 0 || (int) $payment?->payroll_id === (int) $payroll->id;
            });

        $statuses = $this->status->where('model', 'PayrollPayment')->get();

        return [
            'payrolls' => $payrolls,
            'paymentMethods' => PaymentMethod::query()
                ->where('status_id', statusId('PaymentMethod', 'active'))
                ->orderBy('name')
                ->get(),
            'statuses' => $statuses,
            'defaultPaymentStatus' => $statuses->firstWhere('name', 'active')?->ulid,
        ];
    }
}
