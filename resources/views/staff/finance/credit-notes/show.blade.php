@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-8">

        {{-- ================= HEADER ================= --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    Credit Note
                </h1>
                <p class="text-sm text-gray-500">
                    {{ $creditNote->credit_note_number }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                {{ $creditNote->remaining_amount <= 0
                    ? 'bg-green-100 text-green-800'
                    : ($creditNote->total_allocated > 0
                        ? 'bg-yellow-100 text-yellow-800'
                        : 'bg-gray-100 text-gray-800') }}">
                    {{ strtoupper(
                        $creditNote->remaining_amount <= 0 ? 'used' : ($creditNote->total_allocated > 0 ? 'partial' : 'unused'),
                    ) }}
                </span>
                @if ($creditNote->hasRemainingBalance())
                    <a href="{{ route('staff.invoices.index', [
                        'customer_id' => $creditNote->customer_id,
                        'apply_credit' => $creditNote->id,
                    ]) }}"
                        class="crm-btn-primary">
                        ➕ Apply to Invoice
                    </a>
                @endif

            </div>
        </div>

        {{-- ================= META ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">

                <div>
                    <div class="text-gray-500">Customer</div>
                    <div class="font-semibold">
                        {{ $creditNote->customer->registered_company_name ?? $creditNote->customer->contact_name }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">Issued Date</div>
                    <div class="font-semibold">
                        {{ $creditNote->issued_at?->format('d M Y') ?? '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">Reason</div>
                    <div class="font-semibold">
                        {{ $creditNote->reason ?: '—' }}
                    </div>
                </div>

            </div>
        </div>

        {{-- ================= AMOUNTS ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
            <table class="min-w-full border-collapse text-sm">
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 text-gray-600">Total Credit</td>
                        <td class="px-6 py-4 text-right font-semibold">
                            £{{ number_format($creditNote->total_amount, 2) }}
                        </td>
                    </tr>

                    <tr>
                        <td class="px-6 py-4 text-gray-600">Allocated</td>
                        <td class="px-6 py-4 text-right text-green-700 font-medium">
                            £{{ number_format($creditNote->total_allocated, 2) }}
                        </td>
                    </tr>

                    <tr>
                        <td class="px-6 py-4 font-semibold text-gray-900">Remaining</td>
                        <td class="px-6 py-4 text-right font-semibold text-red-600">
                            £{{ number_format($creditNote->remaining_amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- ================= ALLOCATIONS ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    Allocations
                </h3>
            </div>

            @if ($creditNote->allocations->isEmpty())
                <div class="px-6 py-8 text-center text-gray-500">
                    This credit note has not been applied to any invoices yet.
                </div>
            @else
                <table class="min-w-full border-collapse text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                Invoice
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                Applied Date
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                Amount Applied
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                Notes
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @foreach ($creditNote->allocations as $allocation)
                            <tr>
                                <td class="px-6 py-3 font-medium text-blue-600">
                                    <a href="{{ route('staff.invoices.show', $allocation->invoice) }}"
                                        class="hover:underline">
                                        {{ $allocation->invoice->invoice_number }}
                                    </a>
                                </td>

                                <td class="px-6 py-3 text-gray-700">
                                    {{ $allocation->applied_at?->format('d M Y') ?? '—' }}
                                </td>

                                <td class="px-6 py-3 text-right font-semibold text-green-700">
                                    £{{ number_format($allocation->amount_applied, 2) }}
                                </td>

                                <td class="px-6 py-3 text-gray-600">
                                    {{ $allocation->notes ?: '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
@endsection
