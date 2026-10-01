<?php

namespace App\Modules\InventoryItem\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Modules\InventoryItem\Repositories\Contracts\InventoryItemContract;
use App\Modules\InventoryItem\Http\Requests\InventoryItemRequest;
use App\Modules\InventoryItem\Models\InventoryItem;
use App\Modules\InventoryCategory\Models\InventoryCategory;
use App\Modules\Unit\Models\Unit;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Status;

class InventoryItemController extends BaseModuleController
{
    protected $status;

    public function __construct(
        protected InventoryItemContract $inventoryItemRepo
    ){
        $this->status = new Status();
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'name'      => ['label' => 'Name', 'searchable' => 'name'],
            'sku'      => ['label' => 'SKU', 'searchable' => 'sku'],
            'price_per_unit'      => ['label' => 'Price Per Unit', 'searchable' => 'price_per_unit'],
            'total_quantity'      => ['label' => 'Total Qty', 'searchable' => 'total_quantity'],
            'minimum_quantity'      => ['label' => 'Min Qty', 'searchable' => 'minimum_quantity'],
            'status'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'author_id'     => ['label' => 'Author', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        $query = $this->inventoryItemRepo->getAll();
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
        $activeInventoryCatStatusId = Status::where('model', 'InventoryCategory')->where('name', 'active')->value('id');
        $activeUnitStatusId = Status::where('model', 'Unit')->where('name', 'active')->value('id');
        $inventoryCategories = InventoryCategory::where('status_id', $activeInventoryCatStatusId)->get();
        $units = Unit::where('status_id', $activeUnitStatusId)->get();
        return (string) view($this->pathInitialize.'.create_content', get_defined_vars());
    }

    public function store(InventoryItemRequest $request)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $this->inventoryItemRepo->storeModel($payload);
            });
            return successResponse($response, module_message('created', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(InventoryItem $inventoryItem)
    {
        $activeInventoryCatStatusId = Status::where('model', 'InventoryCategory')->where('name', 'active')->value('id');
        $activeUnitStatusId = Status::where('model', 'Unit')->where('name', 'active')->value('id');
        $inventoryCategories = InventoryCategory::where('status_id', $activeInventoryCatStatusId)->get();
        $units = Unit::where('status_id', $activeUnitStatusId)->get();

        $statuses = $this->status->where('model', 'InventoryItem')->get();
        $model = $this->inventoryItemRepo->showModel($inventoryItem);
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(InventoryItemRequest $request, InventoryItem $inventoryItem)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $inventoryItem) {
                $this->inventoryItemRepo->updateModel($inventoryItem, $payload);
            });
            return successResponse($response, module_message('updated', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(InventoryItem $inventoryItem)
    {
        $model = $this->inventoryItemRepo->showModel($inventoryItem);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        try {
            if($this->inventoryItemRepo->softDeleteModel($inventoryItem)) {
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

    public function restore(InventoryItem $inventoryItem)
    {
        try {
            if($this->inventoryItemRepo->restoreModel($inventoryItem)) {
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

    public function forceDelete(InventoryItem $inventoryItem)
    {
        try {
            if ($this->inventoryItemRepo->permanentlyDeleteModel($inventoryItem)) {
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
            $this->inventoryItemRepo->bulkDelete();
            return redirect()->route('back-office.inventory_items.index')->with('success', value: module_message('bulk-deleted', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->inventoryItemRepo->bulkRestore();
            return redirect()->route('back-office.inventory_items.index')->with('success', module_message('bulk-restored', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
