@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- ================= HEADER ================= --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    Delivery & Collection Notes
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    All issued delivery and collection notes
                </p>
            </div>
        </div>

        {{-- ================= TABLE ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
            <table class="min-w-full border-collapse">

                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Note No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Account</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse ($deliveryNotes as $note)
                        @php
                            $customer = $note->customer;
                            $customerName = $customer->registered_company_name ?: $customer->contact_name;
                        @endphp

                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                {{ $note->delivery_note_number }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $note->type === 'delivery' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($note->type) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $note->issued_at?->format('d M Y') }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $customer->account_number }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-900">
                                {{ $customerName }}
                            </td>

                            <td class="px-4 py-3 text-right text-sm space-x-3 whitespace-nowrap">
                                <a href="{{ route('staff.delivery-notes.show', $note) }}"
                                    class="text-blue-600 hover:underline">
                                    View
                                </a>

                                <a href="{{ route('staff.delivery-notes.print', $note) }}"
                                    class="text-gray-600 hover:underline" target="_blank">
                                    Print
                                </a>

                                {{-- EMAIL --}}
                                <form action="{{ route('staff.delivery-notes.email', $note) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <button type="submit" class="text-emerald-600 hover:underline"
                                        onclick="return confirm('Send this delivery note by email?')">
                                        Email
                                    </button>
                                </form>

                                {{-- INVOICE LOGIC --}}
                                @if ($note->invoice_id)
                                    <span class="text-gray-500">
                                        <a href="{{ route('staff.invoices.show', $note->invoice) }}"
                                            class="text-indigo-600 hover:underline">
                                            Invoice: {{ $note->invoice->invoice_number }}
                                        </a>
                                    </span>
                                @else
                                    <form action="{{ route('staff.delivery-notes.convert-to-invoice', $note) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-indigo-600 hover:underline"
                                            onclick="return confirm('Convert this delivery note to an invoice?')">
                                            Convert to Invoice
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No delivery notes found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- ================= PAGINATION ================= --}}
        <div class="mt-6">
            {{ $deliveryNotes->links() }}
        </div>

    </div>
@endsection
