<?php

namespace App\Modules\Payment\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Modules\Payment\Repositories\Contracts\PaymentContract;
use App\Modules\Payment\Http\Requests\PaymentRequest;
use App\Modules\Payment\Models\Payment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Status;
use App\Modules\Event\Models\Event;
use App\Modules\EventCategory\Models\EventCategory;
use App\Modules\InventoryCategory\Models\InventoryCategory;
use App\Modules\InventoryItem\Models\InventoryItem;
use App\Modules\Package\Models\Package;
use App\Modules\PaymentMethod\Models\PaymentMethod;
use App\Modules\Service\Models\Service;

class PaymentController extends BaseModuleController
{
    protected $status;
    protected $eventModel;

    protected $eventCategoryModel;
    protected $inventoryCategoryModel;
    protected $inventoryItemModel;
    protected $paymentMethodModel;
    protected $serviceModel;
    protected $packageModel;

    public function __construct(
        protected PaymentContract $paymentRepo
    ){
        $this->status = new Status();
        $this->eventModel = new Event();
        $this->eventCategoryModel = new EventCategory();
        $this->inventoryCategoryModel = new InventoryCategory();
        $this->inventoryItemModel = new InventoryItem();
        $this->paymentMethodModel = new PaymentMethod();
        $this->serviceModel = new Service();
        $this->packageModel = new Package();

        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'event_number'      => ['label' => 'Event No', 'html' => true, 'searchable' => false],
            'paid_amount'      => ['label' => 'Amount', 'html' => true, 'searchable' => false],
            'payment_method_id'      => ['label' => 'Payment Method', 'html' => true, 'searchable' => false],
            'status_name'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'author_id'     => ['label' => 'Author', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        $query = $this->paymentRepo->getAll();
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
        $row->status_name = '<span class="badge rounded-pill px-3 py-2 '. badgeClass($status) .'">'
                    . strtoupper($status) .
                    '</span>';

        $row->paid_amount = '<span class="badge rounded-pill px-3 py-2 '. badgeClass('paid') .'">Rs.'
                    . $row->amount .
                    '</span>';

        $row->payment_method_id = $row?->paymentMethod?->name ?? '-';
        $row->event_number = $row?->invoice?->event?->event_number ?? '-';

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
        $events = $this->eventModel::query()
                ->whereHas('invoice', function ($query) {
                    $query->where('outstanding_amount', '>', 0);
                })
                ->with([
                    'customer',
                    'invoice',
                ])
                ->get();
        return (string) view($this->pathInitialize.'.create_content', get_defined_vars());
    }

    public function store(PaymentRequest $request)
    {
        $payload = $request->validated();

        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $this->paymentRepo->storeModel($payload);
            });
            return successResponse($response, module_message('created', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(Payment $payment)
    {
        $statuses = $this->status->where('model', 'Payment')->get();
        $model = $this->paymentRepo->showModel($payment);

        $events = $this->eventModel::query()
            ->whereHas('invoice', function ($query) use($model) {
                $query->where('outstanding_amount', '>', 0);
                $query->orWhere('id', $model->invoice_id);
            })
            ->with([
                'customer',
                'invoice',
            ])
            ->get();

        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(PaymentRequest $request, Payment $payment)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $payment) {
                $this->paymentRepo->updateModel($payment, $payload);
            });
            return successResponse($response, module_message('updated', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(Payment $payment)
    {
        $model = $this->paymentRepo->showModel($payment);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(Payment $payment)
    {
        try {
            if($this->paymentRepo->softDeleteModel($payment)) {
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

    public function restore(Payment $payment)
    {
        try {
            if($this->paymentRepo->restoreModel($payment)) {
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

    public function forceDelete(Payment $payment)
    {
        try {
            if ($this->paymentRepo->permanentlyDeleteModel($payment)) {
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
            $this->paymentRepo->bulkDelete();
            return redirect()->route('back-office.payments.index')->with('success', value: module_message('bulk-deleted', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->paymentRepo->bulkRestore();
            return redirect()->route('back-office.payments.index')->with('success', module_message('bulk-restored', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function paymentContent(Request $request, Event $event)
    {
        $formData = $this->getFormData();

        $model = $event->where('ulid', $request->event_id)->first();

        $orders = $model->orders()
            ->with([
                'invoice.status',
                'invoice.payments.status',
                'invoice.payments.paymentMethod',
            ])
            ->get();

        $totalOrderAmount = $orders->sum('grand_total');
        $totalOrderDiscount = $orders->sum('discount');

        // Additional discounts recorded on event invoices.
        $totalInvoiceDiscount = $model->invoices()
            ->sum('discount');

        $totalDiscount = $totalOrderDiscount + $totalInvoiceDiscount;

        $payments = $model->payments()
            ->with(['status', 'paymentMethod'])
            ->get()
            ->filter(fn ($payment) =>
                $payment->status?->name === 'Completed'
            )
            ->sortByDesc('paid_at')
            ->values();

        $totalPaid = $payments->sum('amount');

        $totalPayable = max(
            0,
            $totalOrderAmount - $totalDiscount
        );

        $totalOutstanding = max(
            0,
            $totalPayable - $totalPaid
        );

        return (string) view(
            $this->pathInitialize . '.partials.payment_content',
            array_merge($formData, get_defined_vars())
        );
    }

    private function getFormData(): array
    {
        $activePaymentMethodStatusId = $this->status->where('model', 'PaymentMethod')
            ->where('name', 'active')
            ->value('id');

        return [
            'paymentMethods' => $this->paymentMethodModel->where('status_id', $activePaymentMethodStatusId)->get(),
        ];
    }
}
