<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomerStatusController extends Controller
{
    /**
     * Update customer status with mandatory reason logging.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'new_status' => ['required', 'in:active,on_hold,blocked'],
            'reason'     => ['required', 'string', 'min:5'],
        ]);

        if ($customer->customer_status === $validated['new_status']) {
            return back()->withErrors([
                'new_status' => 'Customer is already in this status.',
            ]);
        }

        DB::transaction(function () use ($customer, $validated) {
            // Log status change
            CustomerStatusHistory::create([
                'customer_id' => $customer->id,
                'old_status'  => $customer->customer_status,
                'new_status'  => $validated['new_status'],
                'reason'      => $validated['reason'],
                'changed_by'  => Auth::id(),
                'changed_at'  => now(),
            ]);

            // Update customer
            $customer->update([
                'customer_status' => $validated['new_status'],
            ]);
        });

        return redirect()
            ->route('customers.edit', $customer)
            ->with('success', 'Customer status updated successfully.');
    }
}
