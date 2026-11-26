<?php

namespace App\Modules\Review\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Modules\Review\Repositories\Contracts\ReviewContract;
use App\Modules\Review\Http\Requests\ReviewRequest;
use App\Modules\Review\Models\Review;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Status;

class ReviewController extends BaseModuleController
{
    protected $status;

    public function __construct(
        protected ReviewContract $reviewRepo
    ){
        $this->status = new Status();
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'customer'      => ['label' => 'Customer', 'html' => true, 'searchable' => false],
            'rating'      => ['label' => 'Rating', 'searchable' => 'rating'],
            'status'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'author_id'     => ['label' => 'Author', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        $query = $this->reviewRepo->getAll();
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

        $row->rating = $row->rating
                ? view('back-office.partials.rating', ['rating' => $row->rating])->render()
                : '-';

        $row->customer = $row->customer
                ? $row->customer?->name ?? '-'
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

    public function store(ReviewRequest $request)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $this->reviewRepo->storeModel($payload);
            });
            return successResponse($response, module_message('created', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(Review $review)
    {
        $statuses = $this->status->where('model', 'Review')->get();
        $model = $this->reviewRepo->showModel($review);
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(ReviewRequest $request, Review $review)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $review) {
                $this->reviewRepo->updateModel($review, $payload);
            });
            return successResponse($response, module_message('updated', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(Review $review)
    {
        $model = $this->reviewRepo->showModel($review);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(Review $review)
    {
        try {
            if($this->reviewRepo->softDeleteModel($review)) {
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

    public function restore(Review $review)
    {
        try {
            if($this->reviewRepo->restoreModel($review)) {
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

    public function forceDelete(Review $review)
    {
        try {
            if ($this->reviewRepo->permanentlyDeleteModel($review)) {
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
            $this->reviewRepo->bulkDelete();
            return redirect()->route('back-office.reviews.index')->with('success', value: module_message('bulk-deleted', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->reviewRepo->bulkRestore();
            return redirect()->route('back-office.reviews.index')->with('success', module_message('bulk-restored', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
