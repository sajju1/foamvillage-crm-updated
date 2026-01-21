<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerAddressController extends Controller
{
    /**
     * Store a new customer address.
     */
    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'address_type'  => ['required', 'in:registered,billing,delivery'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'address_line3' => ['nullable', 'string', 'max:255'],
            'city'          => ['required', 'string', 'max:255'],
            'state_region'  => ['nullable', 'string', 'max:255'],
            'country'       => ['required', 'string', 'max:255'],
            'postcode'      => ['required', 'string', 'max:50'],
            'is_default'    => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($customer, $validated) {

            $type = $validated['address_type'];

            // Registered & Billing: only one active allowed
            if (in_array($type, ['registered', 'billing'], true)) {
                CustomerAddress::where('customer_id', $customer->id)
                    ->where('address_type', $type)
                    ->where('is_active', true)
                    ->update([
                        'is_active'      => false,
                        'is_default'     => false,
                        'deactivated_at' => now(),
                    ]);
            }

            // Delivery default handling
            $isDefault = false;

            if ($type === 'delivery') {
                $activeDeliveryCount = CustomerAddress::where('customer_id', $customer->id)
                    ->where('address_type', 'delivery')
                    ->where('is_active', true)
                    ->count();

                // First delivery must be default
                if ($activeDeliveryCount === 0) {
                    $isDefault = true;
                } elseif (!empty($validated['is_default'])) {
                    $isDefault = true;

                    CustomerAddress::where('customer_id', $customer->id)
                        ->where('address_type', 'delivery')
                        ->where('is_active', true)
                        ->update(['is_default' => false]);
                }
            }

            CustomerAddress::create([
                'customer_id'   => $customer->id,
                'address_type'  => $type,
                'address_line1' => $validated['address_line1'],
                'address_line2' => $validated['address_line2'] ?? null,
                'address_line3' => $validated['address_line3'] ?? null,
                'city'          => $validated['city'],
                'state_region'  => $validated['state_region'] ?? null,
                'country'       => $validated['country'],
                'postcode'      => $validated['postcode'],
                'is_default'    => $isDefault,
                'is_active'     => true,
            ]);
        });

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Address added successfully.');
    }

    /**
     * Show form to create a new address.
     */
    public function create(Request $request, Customer $customer)
    {
        $type = $request->get('type');

        if (!in_array($type, ['registered', 'billing', 'delivery'], true)) {
            abort(404);
        }

        return view('customers.addresses.create', compact('customer', 'type'));
    }

    /**
     * Show form to edit an existing delivery address.
     */
    public function edit(CustomerAddress $address)
    {
        if ($address->address_type !== 'delivery') {
            abort(403, 'Only delivery addresses can be edited.');
        }

        return view('customers.addresses.edit', compact('address'));
    }

    /**
     * Update an existing address.
     */
    public function update(Request $request, CustomerAddress $address)
    {
        $validated = $request->validate([
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'address_line3' => ['nullable', 'string', 'max:255'],
            'city'          => ['required', 'string', 'max:255'],
            'state_region'  => ['nullable', 'string', 'max:255'],
            'country'       => ['required', 'string', 'max:255'],
            'postcode'      => ['required', 'string', 'max:50'],
            'is_default'    => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($address, $validated) {

            if (
                $address->address_type === 'delivery' &&
                !empty($validated['is_default']) &&
                $address->is_active
            ) {
                CustomerAddress::where('customer_id', $address->customer_id)
                    ->where('address_type', 'delivery')
                    ->where('is_active', true)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            $address->update(array_merge(
                $validated,
                [
                    'is_default' => $address->address_type === 'delivery'
                        ? ($validated['is_default'] ?? $address->is_default)
                        : false,
                ]
            ));
        });

        return back()->with('success', 'Address updated successfully.');
    }

    /**
     * Deactivate a customer address (soft deactivate).
     */
    public function deactivate(CustomerAddress $address)
    {
        // ❌ No DB transaction needed for validation logic
        if ($address->is_active) {

            // Registered & Billing must always exist
            if (in_array($address->address_type, ['registered', 'billing'], true)) {
                $activeCount = CustomerAddress::where('customer_id', $address->customer_id)
                    ->where('address_type', $address->address_type)
                    ->where('is_active', true)
                    ->count();

                if ($activeCount <= 1) {
                    return back()->with(
                        'error',
                        'This address type is required and cannot be removed.'
                    );
                }
            }

            // Delivery rules
            if ($address->address_type === 'delivery') {
                $activeDeliveryCount = CustomerAddress::where('customer_id', $address->customer_id)
                    ->where('address_type', 'delivery')
                    ->where('is_active', true)
                    ->count();

                if ($activeDeliveryCount <= 1) {
                    return back()->with(
                        'error',
                        'At least one active delivery address is required.'
                    );
                }

                if ($address->is_default) {
                    return back()->with(
                        'error',
                        'Please set another delivery address as default before deactivating this one.'
                    );
                }
            }
        }

        $address->update([
            'is_active'      => false,
            'is_default'     => false,
            'deactivated_at' => now(),
        ]);

        return back()->with('success', 'Address deactivated successfully.');
    }
}
