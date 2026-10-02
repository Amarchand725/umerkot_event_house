<?php

namespace App\Modules\Expense\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Modules\Expense\Repositories\Contracts\ExpenseContract;
use App\Modules\Expense\Http\Requests\ExpenseRequest;
use App\Modules\Expense\Models\Expense;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Status;

class ExpenseController extends BaseModuleController
{
    protected $status;

    public function __construct(
        protected ExpenseContract $expenseRepo
    ){
        $this->status = new Status();
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'event_id'      => ['label' => 'Event', 'html' => true, 'searchable' => false],
            'expense_category_id'      => ['label' => 'Category', 'html' => true, 'searchable' => false],
            'amount'     => ['label' => 'Amount', 'html' => false, 'searchable' => true],
            'payment_method_id'     => ['label' => 'Payment Method', 'html' => true, 'searchable' => false],
            'reference'     => ['label' => 'Reference', 'html' => false, 'searchable' => true],
            'expense_date'     => ['label' => 'Expense Date', 'html' => false, 'searchable' => true],
            'status'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'author_id'     => ['label' => 'Author', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        $query = $this->expenseRepo->getAll();
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

    public function store(ExpenseRequest $request)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $this->expenseRepo->storeModel($payload);
            });
            return successResponse($response, module_message('created', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(Expense $expense)
    {
        $statuses = $this->status->where('model', 'Expense')->get();
        $model = $this->expenseRepo->showModel($expense);
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $expense) {
                $this->expenseRepo->updateModel($expense, $payload);
            });
            return successResponse($response, module_message('updated', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(Expense $expense)
    {
        $model = $this->expenseRepo->showModel($expense);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(Expense $expense)
    {
        try {
            if($this->expenseRepo->softDeleteModel($expense)) {
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

    public function restore(Expense $expense)
    {
        try {
            if($this->expenseRepo->restoreModel($expense)) {
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

    public function forceDelete(Expense $expense)
    {
        try {
            if ($this->expenseRepo->permanentlyDeleteModel($expense)) {
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
            $this->expenseRepo->bulkDelete();
            return redirect()->route('back-office.expenses.index')->with('success', value: module_message('bulk-deleted', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->expenseRepo->bulkRestore();
            return redirect()->route('back-office.expenses.index')->with('success', module_message('bulk-restored', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
