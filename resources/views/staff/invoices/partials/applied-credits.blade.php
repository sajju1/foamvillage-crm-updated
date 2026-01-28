@if ($invoice->creditAllocations->isNotEmpty())
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm mt-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                Applied Credits
            </h3>
        </div>

        <table class="min-w-full border-collapse text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                        Credit Note
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
                @foreach ($invoice->creditAllocations as $allocation)
                    <tr>
                        <td class="px-6 py-3 font-medium text-gray-900">
                            {{ $allocation->creditNote->credit_note_number }}
                        </td>

                        <td class="px-6 py-3 text-gray-700">
                            {{ $allocation->applied_at?->format('d M Y') ?? '—' }}
                        </td>

                        <td class="px-6 py-3 text-right font-semibold text-red-700">
                            −£{{ number_format($allocation->amount_applied, 2) }}
                        </td>

                        <td class="px-6 py-3 text-gray-600">
                            {{ $allocation->notes ?? '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
