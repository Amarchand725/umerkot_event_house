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

use App\Modules\InventItem\Models\InventItem;
use App\Modules\EventCategory\Models\EventCategory;

class EventController extends BaseModuleController
{
    protected $status;

    public function __construct(
        protected EventContract $eventRepo
    ){
        $this->status = new Status();
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'customer'      => ['label' => 'name', 'html' => true, 'searchable' => false],
            'event'      => ['label' => 'Event Category', 'html' => true, 'searchable' => false],
            'start_date'      => ['label' => 'Start Date', 'searchable' => 'start_date'],
            'total'      => ['label' => 'Total Bill', 'searchable' => 'total'],
            'status'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'payment_status'     => ['label' => 'Payment Status', 'html' => true, 'searchable' => false],
            'author_id'     => ['label' => 'Author', 'html' => true, 'searchable' => false],
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
        $activeEventCatStatusId = Status::where('model', 'EventCategory')->where('name', 'active')->value('id');
        $activeInventoryItemStatusId = Status::where('model', 'InventItem')->where('name', 'active')->value('id');
        $eventCategories = EventCategory::where('status_id', $activeEventCatStatusId)->get();
        $inventoryItems = InventItem::where('status_id', $activeInventoryItemStatusId)->get();

        return (string) view($this->pathInitialize.'.create_content', get_defined_vars());
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
        $activeEventCatStatusId = Status::where('model', 'EventCategory')->where('name', 'active')->value('id');
        $activeInventoryItemStatusId = Status::where('model', 'InventItem')->where('name', 'active')->value('id');
        $eventCategories = EventCategory::where('status_id', $activeEventCatStatusId)->get();
        $inventoryItems = InventItem::where('status_id', $activeInventoryItemStatusId)->get();

        $statuses = $this->status->where('model', 'Event')->get();
        $model = $this->eventRepo->showModel($event);
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
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
