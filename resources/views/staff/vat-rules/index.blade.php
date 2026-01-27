@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">VAT Rules</h1>
            <p class="text-sm text-gray-500">
                Manage VAT rates used for products and invoicing
            </p>
        </div>

        <button
            onclick="document.getElementById('createVatModal').classList.remove('hidden')"
            class="btn-primary"
        >
            + Add VAT Rule
        </button>
    </div>

    {{-- VAT Rules Table --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr class="text-left text-gray-600">
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Rate (%)</th>
                    <th class="px-4 py-3">Default</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse ($vatRules as $vat)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $vat->name }}
                        </td>

                        <td class="px-4 py-3">
                            {{ number_format($vat->rate, 2) }}%
                        </td>

                        <td class="px-4 py-3">
                            @if($vat->is_default)
                                <span class="text-green-600 font-semibold">Yes</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            @if($vat->is_active)
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-right">
                            @if($vat->is_active && !$vat->is_default)
                                <form method="POST"
                                      action="{{ route('staff.vat-rules.deactivate', $vat) }}"
                                      class="inline">
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="text-red-600 hover:underline text-sm"
                                        onclick="return confirm('Deactivate this VAT rule? It will no longer be selectable.')"
                                    >
                                        Deactivate
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-300 text-sm">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            No VAT rules created yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ================= CREATE VAT MODAL ================= --}}
<div id="createVatModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-40"
         onclick="document.getElementById('createVatModal').classList.add('hidden')"></div>

    <div class="relative bg-white rounded-lg shadow-lg max-w-md mx-auto mt-32 p-6">
        <h2 class="text-lg font-semibold mb-4">Add VAT Rule</h2>

        <form method="POST" action="{{ route('staff.vat-rules.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Name
                </label>
                <input
                    type="text"
                    name="name"
                    required
                    class="form-input w-full"
                    placeholder="Standard VAT"
                >
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Rate (%)
                </label>
                <input
                    type="number"
                    name="rate"
                    step="0.01"
                    min="0"
                    required
                    class="form-input w-full"
                    placeholder="20.00"
                >
            </div>

            <div class="mb-6 flex items-center">
                <input
                    type="checkbox"
                    name="is_default"
                    value="1"
                    id="is_default"
                    class="mr-2"
                >
                <label for="is_default" class="text-sm text-gray-700">
                    Set as default VAT
                </label>
            </div>

            <div class="flex justify-end gap-3">
                <button
                    type="button"
                    onclick="document.getElementById('createVatModal').classList.add('hidden')"
                    class="btn-secondary"
                >
                    Cancel
                </button>

                <button type="submit" class="btn-primary">
                    Save VAT Rule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
