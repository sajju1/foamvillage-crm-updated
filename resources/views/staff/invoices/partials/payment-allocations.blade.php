@if ($invoice->paymentAllocations->isNotEmpty())
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm mt-6">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                Payments Applied to This Invoice
            </h3>
        </div>

        {{-- Table --}}
        <table class="min-w-full border-collapse text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                        Date
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                        Source
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                        Reference
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                        Amount Applied
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @foreach ($invoice->paymentAllocations as $allocation)
                    <tr>
                        <td class="px-6 py-3 text-gray-700">
                            {{ $allocation->created_at->format('d M Y') }}
                        </td>

                        <td class="px-6 py-3 text-gray-700">
                            {{ $allocation->payment->method ?? '—' }}
                        </td>

                        <td class="px-6 py-3 text-gray-700">
                            {{ $allocation->payment->reference ?? 'Payment #' . $allocation->payment_id }}
                        </td>

                        <td class="px-6 py-3 text-right font-semibold text-green-700">
                            £{{ number_format($allocation->allocated_amount, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            {{-- Footer total --}}
            <tfoot class="bg-gray-50 border-t">
                <tr>
                    <td colspan="3" class="px-6 py-3 text-right font-semibold text-gray-900">
                        Total Paid
                    </td>
                    <td class="px-6 py-3 text-right font-semibold text-green-800">
                        £{{ number_format($invoice->total_paid, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
@endif
