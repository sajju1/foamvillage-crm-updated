<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use Illuminate\Support\Facades\Auth;

class OrderAcknowledgementController extends Controller
{
    /**
     * Acknowledge an order (staff action)
     */
    public function acknowledge(Order $order)
    {
        // Only submitted orders can be acknowledged
        if (! $order->canBeAcknowledged()) {
            abort(403);
        }

        $order->update([
            'status'          => 'acknowledged',
            'acknowledged_at' => now(),
        ]);

        return redirect(
            route('orders.show', [$order->customer_id, $order]) . '?context=staff'
        );
    }
}
