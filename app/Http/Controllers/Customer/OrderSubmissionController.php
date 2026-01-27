<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer\Customer;


class OrderSubmissionController extends Controller
{
    /**
     * Submit a draft order
     */

    public function submit(Customer $customer, Order $order)
    {
        // Safety check
        if ($order->customer_id !== $customer->id) {
            abort(403);
        }

        if ($order->status !== 'draft') {
            abort(403, 'Order cannot be submitted.');
        }

        $order->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        if (request('context') === 'staff') {
            return redirect()
                ->route('staff.orders.index')
                ->with('success', 'Order amended and re-submitted successfully.');
        }

        return redirect()
            ->route('orders.index', $customer)
            ->with('success', 'Order submitted successfully.');
    }


    /**
     * -------------------------------------
     * Internal helpers
     * -------------------------------------
     */

    protected function authorizeCustomerOrder(Order $order): void
    {
        $customer = Auth::user()->customer;

        if ($order->customer_id !== $customer->id) {
            abort(403);
        }
    }
}
