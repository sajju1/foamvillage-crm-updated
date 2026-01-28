@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">

    {{-- ================= HEADER ================= --}}
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">
            Create Credit Note
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Issue a credit note for a customer
        </p>
    </div>

    {{-- ================= FORM ================= --}}
    <form method="POST"
          action="{{ route('staff.credit-notes.store') }}"
          class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 space-y-6">

        @csrf

        {{-- Customer --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Customer
            </label>
            <select name="customer_id"
                    required
                    class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="">Select customer</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">
                        {{ $customer->registered_company_name ?? $customer->contact_name }}
                    </option>
                @endforeach
            </select>
            @error('customer_id')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Issued Date --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Issued Date
            </label>
            <input type="date"
                   name="issued_at"
                   value="{{ old('issued_at', now()->toDateString()) }}"
                   required
                   class="w-full border border-gray-300 rounded px-3 py-2">
            @error('issued_at')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Amount --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Credit Amount (£)
            </label>
            <input type="number"
                   name="total_amount"
                   step="0.01"
                   min="0.01"
                   value="{{ old('total_amount') }}"
                   required
                   class="w-full border border-gray-300 rounded px-3 py-2">
            @error('total_amount')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Reason --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Reason (optional)
            </label>
            <textarea name="reason"
                      rows="3"
                      class="w-full border border-gray-300 rounded px-3 py-2">{{ old('reason') }}</textarea>
            @error('reason')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('staff.credit-notes.index') }}"
               class="crm-btn-secondary">
                Cancel
            </a>

            <button type="submit"
                    class="crm-btn-primary">
                Create Credit Note
            </button>
        </div>

    </form>

</div>
@endsection
