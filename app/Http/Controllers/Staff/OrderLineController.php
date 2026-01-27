<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use App\Models\Orders\OrderLine;
use Illuminate\Http\Request;

class OrderLineController extends Controller
{
    /**
     * Update processed quantity for an order line
     */
    public function updateProcessedQuantity(Request $request, OrderLine $orderLine)
    {
        $request->validate([
            'processed_quantity' => ['required', 'integer', 'min:0'],
        ]);

        // Do not allow updates on cancelled lines
        if ($orderLine->isCancelled()) {
            abort(403);
        }

        $processed = (int) $request->input('processed_quantity');

        // Cap processed quantity to requested quantity
        $processed = min($processed, $orderLine->requested_quantity);

        $orderLine->processed_quantity = $processed;

        // Update line status
        if ($processed === 0) {
            $orderLine->line_status = 'pending';
        } elseif ($processed < $orderLine->requested_quantity) {
            $orderLine->line_status = 'pending';
        } else {
            $orderLine->line_status = 'fulfilled';
        }

        $orderLine->save();

        // Update parent order status if needed
        $this->syncOrderStatus($orderLine->order);

        return back()->with('success', 'Processed quantity updated.');
    }

    /**
     * Cancel a pending order line
     */
    public function cancel(OrderLine $orderLine)
    {
        // Only pending lines can be cancelled
        if (! $orderLine->isPending()) {
            abort(403);
        }

        $orderLine->update([
            'line_status'       => 'cancelled',
            'processed_quantity'=> 0,
        ]);

        $this->syncOrderStatus($orderLine->order);

        return back()->with('success', 'Pending item cancelled.');
    }

    /**
     * -------------------------------------
     * Internal helpers
     * -------------------------------------
     */

    protected function syncOrderStatus(Order $order): void
    {
        $lines = $order->lines;

        if ($lines->every(fn ($line) => $line->isCancelled() || $line->isFulfilled())) {
            $order->status = 'fulfilled';
        } elseif ($lines->contains(fn ($line) => $line->isFulfilled())) {
            $order->status = 'partially_fulfilled';
        } else {
            $order->status = 'in_progress';
        }

        $order->save();
    }
}
