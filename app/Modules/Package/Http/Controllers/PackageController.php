<?php

namespace App\Modules\Package\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Modules\Package\Repositories\Contracts\PackageContract;
use App\Modules\Package\Http\Requests\PackageRequest;
use App\Modules\Package\Models\Package;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Status;
use App\Modules\InventoryCategory\Models\InventoryCategory;
use App\Modules\InventoryItem\Models\InventoryItem;
use App\Modules\Service\Models\Service;

class PackageController extends BaseModuleController
{
    protected $statusModel;
    protected $inventoryCategoryModel;
    protected $inventoryItemModel;
    protected $serviceModel;


    public function __construct(
        protected PackageContract $packageRepo
    ){
        $this->statusModel = new Status();
        $this->inventoryCategoryModel = new InventoryCategory();
        $this->inventoryItemModel = new InventoryItem();
        $this->serviceModel = new Service();

        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'name'      => ['label' => 'name', 'searchable' => 'name'],
            'price'      => ['label' => 'price', 'html' => true, 'searchable' => 'price'],
            'discount'      => ['label' => 'discount', 'searchable' => 'discount'],
            'status_name'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'author_id'     => ['label' => 'Author', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        $query = $this->packageRepo->getAll();
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

        $row->price = view('back-office.packages.price', ['model' => $row])->render();

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

    public function store(PackageRequest $request)
    {
        $payload = $request->validated();
        
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $response = $this->packageRepo->storeModel($payload);
            });
            return successResponse($response, module_message('created', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(Package $package)
    {
        $statuses = $this->statusModel->where('model', 'Package')->get();
        $model = $this->packageRepo->showModel($package);
        $formData = $this->getFormData();
        
        return (string) view(
            $this->pathInitialize . '.edit_content',
            array_merge($formData, get_defined_vars())
        );
    }

    public function update(PackageRequest $request, Package $package)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $package) {
                $response = $this->packageRepo->updateModel($package, $payload);
            });
            return successResponse($response, module_message('updated', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(Package $package)
    {
        $model = $this->packageRepo->showModel($package);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(Package $package)
    {
        try {
            if($this->packageRepo->softDeleteModel($package)) {
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

    public function restore(Package $package)
    {
        try {
            if($this->packageRepo->restoreModel($package)) {
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

    public function forceDelete(Package $package)
    {
        try {
            if ($this->packageRepo->permanentlyDeleteModel($package)) {
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
            $this->packageRepo->bulkDelete();
            return redirect()->route('back-office.packages.index')->with('success', value: module_message('bulk-deleted', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->packageRepo->bulkRestore();
            return redirect()->route('back-office.packages.index')->with('success', module_message('bulk-restored', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function getFormData(): array
    {
        return [
            'inventoryCategories' => $this->inventoryCategoryModel->where('status_id', statusId('InventoryCategory', 'active'))->get(),
            'inventoryItems'   => $this->inventoryItemModel->where('status_id', statusId('InventoryItem', 'active'))->get(),
            'inventoryServices' => $this->serviceModel->where('status_id', statusId('Service', 'active'))->get(),
        ];
    }
}
