<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use App\Models\Customer\Customer;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * List all orders (staff view)
     */
    public function index()
    {
        $orders = Order::query()
            ->with('customer')
            ->latest()
            ->get();

        return view('staff.orders.index', compact('orders'));
    }

    /**
     * Show customer selector to create a new order
     */
    public function create()
    {
        $customers = Customer::query()
            ->orderBy('account_number')
            ->get(['id', 'account_number']);

        return view('staff.orders.create', compact('customers'));
    }

    /**
     * Store a new draft order for selected customer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);

        // Generate next order number (company-scoped)
        $lastOrderNumber = Order::where('company_id', $customer->company_id)
            ->orderByDesc('id')
            ->value('order_number');

        $nextNumber = 1;

        if ($lastOrderNumber) {
            // Extract numeric part (e.g. ORD-000123 → 123)
            $nextNumber = (int) preg_replace('/\D/', '', $lastOrderNumber) + 1;
        }

        $orderNumber = 'ORD-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        $order = Order::create([
            'company_id'   => $customer->company_id,
            'customer_id'  => $customer->id,
            'order_number' => $orderNumber,
            'status'       => 'draft',
        ]);

        return redirect()->route('orders.show', [
            $customer,
            $order,
        ]);
    }



    /**
     * Show order details (staff view - reserved for future)
     */
    public function show(Order $order)
    {
        return redirect()->route('orders.show', [
            $order->customer_id,
            $order,
        ]);
    }

    public function amend(Order $order)
    {
        // Only allow amendment of submitted orders
        abort_if($order->status !== 'submitted', 403);

        $order->update([
            'status' => 'draft',
        ]);

        // Redirect back into customer-scoped order editing flow
        return redirect(
            route('orders.add-products', [$order->customer, $order]) . '?context=staff&mode=amend'
        )->with('success', 'Order reopened for amendment.');
    }
}
