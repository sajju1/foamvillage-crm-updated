@php
    $registered = $customer->addresses->where('address_type', 'registered')->where('is_active', true)->first();
    $billing = $customer->addresses->where('address_type', 'billing')->where('is_active', true)->first();

    $deliveryActive = $customer->addresses->where('address_type', 'delivery')->where('is_active', true);
    $deliveryInactive = $customer->addresses->where('address_type', 'delivery')->where('is_active', false);

    $defaultDelivery = $deliveryActive->firstWhere('is_default', true);

    $fields = ['address_line1', 'address_line2', 'address_line3', 'city', 'state_region', 'postcode', 'country'];

    $fmt = function ($a) {
        if (!$a) {
            return '—';
        }
        $parts = array_filter([
            $a->address_line1,
            $a->address_line2,
            $a->address_line3,
            trim(($a->city ?? '') . ($a->state_region ?? '' ? ', ' . $a->state_region : '')),
            $a->postcode,
            $a->country,
        ]);
        return implode('<br>', $parts);
    };
@endphp

<div x-data="{ regOpen: false, billOpen: false, delAddOpen: false, delManageOpen: false }" class="bg-white shadow rounded p-6 space-y-4">

    <div class="flex items-center justify-between border-b pb-3">
        <h2 class="text-lg font-semibold">Addresses</h2>
        <div class="text-xs text-gray-500">
            Registered + Billing required • Multiple delivery allowed • One default
        </div>
    </div>

    {{-- 3 COLUMN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- REGISTERED --}}
        <div class="border rounded p-4 space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <div class="font-semibold">Registered</div>
                    <div class="text-xs text-gray-500">Required</div>
                </div>

                <button type="button" class="text-sm text-blue-600 hover:underline" @click="regOpen = true">
                    {{ $registered ? 'Replace' : 'Add' }}
                </button>
            </div>

            @if ($registered)
                <div class="text-sm bg-gray-50 border rounded p-3">{!! $fmt($registered) !!}</div>
            @else
                <div class="text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 p-3 rounded">
                    Registered address is required.
                </div>
            @endif
        </div>

        {{-- BILLING --}}
        <div class="border rounded p-4 space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <div class="font-semibold">Billing</div>
                    <div class="text-xs text-gray-500">Required</div>
                </div>

                <div class="flex items-center gap-3">
                    @if ($registered)
                        {{-- Server-side copy (always works) --}}
                        <form method="POST" action="{{ route('customers.addresses.store', $customer) }}">
                            @csrf
                            <input type="hidden" name="address_type" value="billing">
                            @foreach ($fields as $field)
                                <input type="hidden" name="{{ $field }}" value="{{ $registered->$field }}">
                            @endforeach
                            <button type="submit" class="text-sm text-blue-600 hover:underline">
                                Copy from registered
                            </button>
                        </form>
                    @endif

                    <button type="button" class="text-sm text-blue-600 hover:underline" @click="billOpen = true">
                        {{ $billing ? 'Replace' : 'Add' }}
                    </button>
                </div>
            </div>

            @if ($billing)
                <div class="text-sm bg-gray-50 border rounded p-3">{!! $fmt($billing) !!}</div>
            @else
                <div class="text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 p-3 rounded">
                    Billing address is required.
                </div>
            @endif
        </div>

        {{-- DELIVERY --}}
        <div class="border rounded p-4 space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <div class="font-semibold">Delivery</div>
                    <div class="text-xs text-gray-500">
                        Active: {{ $deliveryActive->count() }} • History: {{ $deliveryInactive->count() }}
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if ($registered)
                        {{-- Server-side copy (always works) --}}
                        <form method="POST" action="{{ route('customers.addresses.store', $customer) }}">
                            @csrf
                            <input type="hidden" name="address_type" value="delivery">
                            @foreach ($fields as $field)
                                <input type="hidden" name="{{ $field }}" value="{{ $registered->$field }}">
                            @endforeach
                            <button type="submit" class="text-sm text-blue-600 hover:underline">
                                Copy from registered
                            </button>
                        </form>
                    @endif

                    <button type="button" class="text-sm text-blue-600 hover:underline" @click="delAddOpen = true">
                        Add
                    </button>

                    <button type="button" class="text-sm text-blue-600 hover:underline" @click="delManageOpen = true">
                        Manage
                    </button>
                </div>
            </div>

            @if ($defaultDelivery)
                <div class="text-sm bg-gray-50 border rounded p-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">{!! $fmt($defaultDelivery) !!}</div>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Default</span>
                    </div>
                </div>
            @elseif ($deliveryActive->isEmpty())
                <div class="text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 p-3 rounded">
                    At least one delivery address is required.
                </div>
            @else
                <div class="text-sm bg-gray-50 border rounded p-3 text-gray-600">
                    No default set (active delivery addresses exist).
                </div>
            @endif

            @if ($deliveryActive->count() > 1)
                <div class="text-xs text-gray-500">
                    Other active delivery addresses hidden — use “Manage”.
                </div>
            @endif
        </div>

    </div>

    {{-- OVERLAY --}}
    <div x-show="regOpen || billOpen || delAddOpen || delManageOpen" x-cloak class="fixed inset-0 z-40 bg-black/50"
        @click="regOpen=false; billOpen=false; delAddOpen=false; delManageOpen=false;">
    </div>

    {{-- REGISTERED MODAL --}}
    <div x-show="regOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded shadow-lg" @click.stop>
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-semibold">{{ $registered ? 'Replace' : 'Add' }} Registered Address</h3>
                <button type="button" class="text-xl text-gray-500 hover:text-gray-700"
                    @click="regOpen=false">×</button>
            </div>

            <form method="POST" action="{{ route('customers.addresses.store', $customer) }}" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="address_type" value="registered">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Address line 1</label>
                        <input name="address_line1" class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Address line 2</label>
                        <input name="address_line2" class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Address line 3</label>
                        <input name="address_line3" class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">City</label>
                        <input name="city" class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">State / Region</label>
                        <input name="state_region" class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Postcode</label>
                        <input name="postcode" class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-600 mb-1">Country</label>
                        <input name="country" class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 border rounded" @click="regOpen=false">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                        {{ $registered ? 'Replace' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- BILLING MODAL --}}
    <div x-show="billOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded shadow-lg" @click.stop>
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-semibold">{{ $billing ? 'Replace' : 'Add' }} Billing Address</h3>
                <button type="button" class="text-xl text-gray-500 hover:text-gray-700"
                    @click="billOpen=false">×</button>
            </div>

            <form method="POST" action="{{ route('customers.addresses.store', $customer) }}" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="address_type" value="billing">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Address line 1</label>
                        <input name="address_line1" class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Address line 2</label>
                        <input name="address_line2" class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Address line 3</label>
                        <input name="address_line3" class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">City</label>
                        <input name="city" class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">State / Region</label>
                        <input name="state_region" class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Postcode</label>
                        <input name="postcode" class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-600 mb-1">Country</label>
                        <input name="country" class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 border rounded" @click="billOpen=false">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                        {{ $billing ? 'Replace' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DELIVERY ADD MODAL --}}
    <div x-show="delAddOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded shadow-lg" @click.stop>
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-semibold">Add Delivery Address</h3>
                <button type="button" class="text-xl text-gray-500 hover:text-gray-700"
                    @click="delAddOpen=false">×</button>
            </div>

            <form method="POST" action="{{ route('customers.addresses.store', $customer) }}" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="address_type" value="delivery">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Address line 1</label>
                        <input name="address_line1" class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Address line 2</label>
                        <input name="address_line2" class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Address line 3</label>
                        <input name="address_line3" class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">City</label>
                        <input name="city" class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">State / Region</label>
                        <input name="state_region" class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Postcode</label>
                        <input name="postcode" class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-600 mb-1">Country</label>
                        <input name="country" class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_default" value="1">
                    Set as default delivery address
                </label>

                <div class="flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 border rounded" @click="delAddOpen=false">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                        Add
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DELIVERY MANAGE MODAL --}}
    <div x-show="delManageOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-4xl rounded shadow-lg flex flex-col max-h-[85vh]" @click.stop>
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-semibold">Manage Delivery Addresses</h3>
                <button type="button" class="text-xl text-gray-500 hover:text-gray-700"
                    @click="delManageOpen=false">×</button>
            </div>

            <div class="p-6 overflow-y-auto space-y-6">

                <div>
                    <div class="font-semibold mb-2">Active</div>

                    @if ($deliveryActive->isEmpty())
                        <div class="text-sm text-gray-600 bg-gray-50 border rounded p-3">
                            No active delivery addresses.
                        </div>
                    @else
                        <table class="w-full text-sm border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="p-2 text-left">Address</th>
                                    <th class="p-2 text-left">Postcode</th>
                                    <th class="p-2 text-left">Country</th>
                                    <th class="p-2 text-left">Default</th>
                                    <th class="p-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deliveryActive as $delivery)
                                    <tr class="border-t">
                                        <td class="p-2">
                                            {{ $delivery->address_line1 }}, {{ $delivery->city }}
                                        </td>

                                        <td class="p-2">{{ $delivery->postcode }}</td>
                                        <td class="p-2">{{ $delivery->country }}</td>

                                        <td class="p-2">
                                            @if ($delivery->is_default)
                                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                    Default
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td class="p-2 text-right space-x-2">

                                            {{-- SET AS DEFAULT --}}
                                            @if (!$delivery->is_default)
                                                <form method="POST"
                                                    action="{{ route('customers.addresses.update', $delivery) }}"
                                                    class="inline">
                                                    @csrf
                                                    @method('PUT')

                                                    {{-- REQUIRED FIELDS (existing values) --}}
                                                    <input type="hidden" name="address_line1"
                                                        value="{{ $delivery->address_line1 }}">
                                                    <input type="hidden" name="address_line2"
                                                        value="{{ $delivery->address_line2 }}">
                                                    <input type="hidden" name="address_line3"
                                                        value="{{ $delivery->address_line3 }}">
                                                    <input type="hidden" name="city"
                                                        value="{{ $delivery->city }}">
                                                    <input type="hidden" name="state_region"
                                                        value="{{ $delivery->state_region }}">
                                                    <input type="hidden" name="postcode"
                                                        value="{{ $delivery->postcode }}">
                                                    <input type="hidden" name="country"
                                                        value="{{ $delivery->country }}">

                                                    {{-- SWITCH DEFAULT --}}
                                                    <input type="hidden" name="is_default" value="1">

                                                    <button class="text-blue-600 text-xs hover:underline">
                                                        Set default
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- DEACTIVATE --}}
                                            @if (!$delivery->is_default)
                                                <form method="POST"
                                                    action="{{ route('customers.addresses.deactivate', $delivery) }}"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button class="text-red-600 text-xs">
                                                        Deactivate
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400">
                                                    Default cannot be deactivated
                                                </span>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    @endif
                </div>

                <div>
                    <div class="font-semibold mb-2">History</div>
                    @if ($deliveryInactive->isEmpty())
                        <div class="text-sm text-gray-600 bg-gray-50 border rounded p-3">
                            No delivery history.
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($deliveryInactive as $addr)
                                <div class="border rounded p-3 text-sm bg-gray-50">
                                    {!! $fmt($addr) !!}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button type="button" class="px-4 py-2 border rounded" @click="delManageOpen=false">Close</button>
            </div>
        </div>
    </div>

</div>
