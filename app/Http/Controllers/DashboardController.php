<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Invoice;

use App\Modules\Event\Models\Event;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Models\Payment;
use App\Modules\Expense\Models\Expense;

class DashboardController extends Controller
{
    protected $orderModel;
    protected $invoiceModel;
    protected $paymentModel;
    protected $eventModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->invoiceModel = new Invoice();
        $this->paymentModel = new Payment();
        $this->eventModel = new Event();
    }

    public function index()
    {
        $title = Auth::user()->name . "'s Dashboard";

        $today = today();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        /*
        |--------------------------------------------------------------------------
        | Order Statistics
        |--------------------------------------------------------------------------
        */

        $totalOrders = $this->orderModel->count();

        $monthlyOrders = $this->orderModel
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Financial Statistics
        |--------------------------------------------------------------------------
        */

        $totalPayable = $this->orderModel->sum('payable_amount');

        $totalPaid = $this->paymentModel
            ->whereHas('status', function ($query) {
                $query->whereIn('name', ['Completed', 'Paid']);
            })
            ->sum('amount');

        $totalOutstanding = max(0, $totalPayable - $totalPaid);

        $monthlyReceived = $this->paymentModel
            ->whereHas('status', function ($query) {
                $query->whereIn('name', ['Completed', 'Paid']);
            })
            ->whereBetween('paid_at', [$monthStart, $monthEnd])
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Operational Expense Statistics
        |--------------------------------------------------------------------------
        | Soft-deleted expenses are automatically excluded by the model.
        | Expenses are recorded against events or as general expenses.
        */

        $totalExpenses = Expense::sum('amount');

        $monthlyExpenses = Expense::whereBetween('expense_date', [
            $monthStart->toDateString(),
            $monthEnd->toDateString()
        ])->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Revenue Statistics
        |--------------------------------------------------------------------------
        | Net Revenue = Total Order Payable - Total Operational Expenses
        */

        $totalRevenue = $totalPayable - $totalExpenses;

        $monthlyRevenue = $this->orderModel
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('payable_amount') - $monthlyExpenses;

        /*
        |--------------------------------------------------------------------------
        | Invoice Statistics
        |--------------------------------------------------------------------------
        */

        $totalInvoices = $this->invoiceModel->count();

        $monthlyInvoices = $this->invoiceModel
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Upcoming Events
        |--------------------------------------------------------------------------
        */

        $upcomingEvents = $this->eventModel
            ->whereDate('start_date', '>=', $today)
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent Orders
        |--------------------------------------------------------------------------
        */

        $recentOrders = $this->orderModel
            ->latest()
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent Payments
        |--------------------------------------------------------------------------
        */

        $recentPayments = $this->paymentModel
            ->with(['paymentMethod', 'status'])
            ->latest()
            ->limit(8)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Monthly Financial Chart
        |--------------------------------------------------------------------------
        */

        $monthlyFinancials = collect(range(1, 12))->map(function ($month) {

            $start = now()->startOfYear()->month($month)->startOfMonth();
            $end = $start->copy()->endOfMonth();

            $invoiceAmount = $this->invoiceModel
                ->whereBetween('issued_at', [
                    $start->toDateString(),
                    $end->toDateString()
                ])
                ->sum('payable_amount');

            $receivedAmount = $this->paymentModel
                ->whereHas('status', function ($query) {
                    $query->whereIn('name', ['Completed', 'Paid']);
                })
                ->whereBetween('paid_at', [$start, $end])
                ->sum('amount');

            $expenseAmount = Expense::whereBetween('expense_date', [
                $start->toDateString(),
                $end->toDateString()
            ])->sum('amount');

            return [
                'month' => $start->format('M'),
                'invoice' => (float) $invoiceAmount,
                'received' => (float) $receivedAmount,
                'expenses' => (float) $expenseAmount,
                'revenue' => (float) ($invoiceAmount - $expenseAmount),
            ];
        });

        return view('back-office.dashboard.index', get_defined_vars());
    }
}
