<?php

namespace App\Modules\Event\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Modules\Event\Repositories\Contracts\EventContract;
use App\Modules\Event\Http\Requests\EventRequest;
use App\Modules\Event\Models\Event;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Status;

use App\Modules\InventoryItem\Models\InventoryItem;
use App\Modules\EventCategory\Models\EventCategory;
use App\Modules\InventoryCategory\Models\InventoryCategory;
use App\Modules\PaymentMethod\Models\PaymentMethod;
use App\Modules\Service\Models\Service;

class EventController extends BaseModuleController
{
    protected $status;
    protected $eventCategory;
    protected $inventoryCategory;
    protected $inventoryItem;
    protected $paymentMethod;
    protected $service;

    public function __construct(
        protected EventContract $eventRepo
    ){
        $this->status = new Status();
        $this->eventCategory = new EventCategory();
        $this->inventoryCategory = new InventoryCategory();
        $this->inventoryItem = new InventoryItem();
        $this->paymentMethod = new PaymentMethod();
        $this->service = new Service();

        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'customer'      => ['label' => 'Customer', 'html' => true, 'searchable' => false],
            'event'      => ['label' => 'Event', 'html' => true, 'searchable' => false],
            'start_date'      => ['label' => 'Date & Time', 'searchable' => 'start_date'],
            'total'      => ['label' => 'Total Bill', 'searchable' => 'total'],
            'status'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'payment_status'     => ['label' => 'Payment Status', 'html' => true, 'searchable' => false],
            // 'author_id'     => ['label' => 'Author', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        $query = $this->eventRepo->getAll();
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
        $formData = $this->getFormData();

        return (string) view(
            $this->pathInitialize . '.create_content',
            array_merge($formData, get_defined_vars())
        );
    }

    public function store(EventRequest $request)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $this->eventRepo->storeModel($payload);
            });
            return successResponse($response, module_message('created', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    public function edit(Event $event)
    {
        $formData = $this->getFormData();

        $statuses = $this->status->where('model', 'Event')->get();
        $model = $this->eventRepo->showModel($event);

        return (string) view(
            $this->pathInitialize . '.edit_content',
            array_merge($formData, get_defined_vars())
        );
    }

    public function update(EventRequest $request, Event $event)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $event) {
                $this->eventRepo->updateModel($event, $payload);
            });
            return successResponse($response, module_message('updated', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(Event $event)
    {
        $model = $this->eventRepo->showModel($event);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    private function getFormData(): array
    {
        $activeEventCatStatusId = $this->status->where('model', 'EventCategory')
            ->where('name', 'active')
            ->value('id');

        $activeInventoryCatStatusId = $this->status->where('model', 'InventoryCategory')
            ->where('name', 'active')
            ->value('id');

        $activeInventoryItemStatusId = $this->status->where('model', 'InventoryItem')
            ->where('name', 'active')
            ->value('id');

        $activePaymentMethodStatusId = $this->status->where('model', 'PaymentMethod')
            ->where('name', 'active')
            ->value('id');

        $activeServiceStatusId = $this->status->where('model', 'Service')
            ->where('name', 'active')
            ->value('id');

        return [
            'eventCategories' => $this->eventCategory->where('status_id', $activeEventCatStatusId)->get(),
            'inventoryCategories' => $this->inventoryCategory->where('status_id', $activeInventoryCatStatusId)->get(),
            'inventoryItems'   => $this->inventoryItem->where('status_id', $activeInventoryItemStatusId)->get(),
            'paymentMethods' => $this->paymentMethod->where('status_id', $activePaymentMethodStatusId)->get(),
            'services' => $this->service->where('status_id', $activeServiceStatusId)->get(),
        ];
    }

    public function destroy(Event $event)
    {
        try {
            if($this->eventRepo->softDeleteModel($event)) {
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

    public function restore(Event $event)
    {
        try {
            if($this->eventRepo->restoreModel($event)) {
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

    public function forceDelete(Event $event)
    {
        try {
            if ($this->eventRepo->permanentlyDeleteModel($event)) {
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
            $this->eventRepo->bulkDelete();
            return redirect()->route('back-office.events.index')->with('success', value: module_message('bulk-deleted', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->eventRepo->bulkRestore();
            return redirect()->route('back-office.events.index')->with('success', module_message('bulk-restored', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
