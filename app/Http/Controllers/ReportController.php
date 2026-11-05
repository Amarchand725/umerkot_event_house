<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends BaseModuleController
{
    protected string $pathInitialize = 'reports';

    protected $eventModel;
    protected $orderModel;
    protected $customerModel;
    protected $inventoryItemModel;

    public function __construct()
    {
        $this->eventModel = new Event();
        $this->orderModel = new Order();
        $this->customerModel = new Customer();
        $this->inventoryItemModel = new InventoryItem();

        // Initialize common module variables automatically
        $this->autoInit();
    }

    /**
     * Reports dashboard.
     */
    public function index()
    {
        $stats = [
            'events' => $this->eventModel->count(),
            'orders' => $this->orderModel->count(),
            'customers' => $this->customerModel->count(),
        ];

        return view(strtolower($this->pathInitialize.'.index'), $this->viewWithVars(get_defined_vars()));
    }

    /**
     * Event reports.
     */
    public function events(Request $request)
    {
        $query = $this->eventModel->with(['customer', 'orders']);

        $this->applyDateFilter($query, $request, 'event_date');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('event_name', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customer) use ($search) {
                        $customer->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        $events = $query->latest('event_date')
            ->paginate(15)
            ->withQueryString();

        return view($this->pathInitialize . '.events', compact('events'));
    }

    /**
     * Order reports.
     */
    public function orders(Request $request)
    {
        $query = $this->orderModel->with(['event.customer']);

        $this->applyDateFilter($query, $request, 'created_at');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('event.customer', function ($customer) use ($search) {
                        $customer->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        $orders = $query->latest()
            ->paginate(15)
            ->withQueryString();

        return view($this->pathInitialize . '.orders', compact('orders'));
    }

    /**
     * Payment reports.
     */
    public function payments(Request $request)
    {
        // Reuse the existing event-wise payment calculation.
        $query = $this->eventModel->with([
            'orders',
            // Load the same payment relationships used
            // by your existing payment summary.
        ]);

        $this->applyDateFilter($query, $request, 'event_date');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('customer', function ($customer) use ($search) {
                $customer->where('name', 'like', "%{$search}%");
            });
        }

        $events = $query->latest('event_date')
            ->paginate(15)
            ->withQueryString();

        return view($this->pathInitialize . '.payments', compact('events'));
    }

    /**
     * Settlement reports.
     */
    public function settlements(Request $request)
    {
        $query = $this->eventModel->with([
            'orders',
            // Add return inspection relationships here.
        ]);

        $this->applyDateFilter($query, $request, 'event_date');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('customer', function ($customer) use ($search) {
                $customer->where('name', 'like', "%{$search}%");
            });
        }

        $events = $query->latest('event_date')
            ->paginate(15)
            ->withQueryString();

        return view($this->pathInitialize . '.settlements', compact('events'));
    }

    /**
     * Inventory reports.
     */
    public function inventory(Request $request)
    {
        $query = $this->inventoryItemModel->query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('name', 'like', "%{$search}%");
        }

        $items = $query->latest()
            ->paginate(15)
            ->withQueryString();

        return view($this->pathInitialize . '.inventory', compact('items'));
    }

    /**
     * Customer reports.
     */
    public function customers(Request $request)
    {
        $query = $this->customerModel->with(['events']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()
            ->paginate(15)
            ->withQueryString();

        return view($this->pathInitialize . '.customers', compact('customers'));
    }

    /**
     * Reusable date filters.
     */
    private function applyDateFilter($query, Request $request, string $column)
    {
        if ($request->filled('from_date')) {
            $query->whereDate($column, '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate($column, '<=', $request->to_date);
        }

        return $query;
    }
}
