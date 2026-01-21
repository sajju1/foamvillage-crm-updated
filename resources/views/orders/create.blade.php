@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-6 py-6">

    <h1 class="text-xl font-semibold mb-6">Create Order (Staff)</h1>

    @if($errors->any())
        <div class="mb-4 text-red-600">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('orders.store') }}" class="bg-white shadow rounded p-4">
        @csrf

        <div class="mb-4">
            <label class="block text-sm mb-1">Customer ID</label>
            <input
                type="number"
                name="customer_id"
                class="w-full border rounded px-3 py-2"
                placeholder="Enter existing customer ID"
                required
            >
        </div>

        <div class="mb-4">
            <label class="block text-sm mb-1">Internal Notes (optional)</label>
            <textarea
                name="internal_notes"
                class="w-full border rounded px-3 py-2"
                rows="3"
            ></textarea>
        </div>

        <div class="flex justify-end">
            <button class="bg-gray-900 text-white px-4 py-2 rounded">
                Create Draft Order
            </button>
        </div>
    </form>

</div>
@endsection
