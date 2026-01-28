@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- ================= HEADER ================= --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    Credit Notes
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    All issued credit notes
                </p>
            </div>

            <a href="{{ route('staff.credit-notes.create') }}" class="crm-btn-primary">
                ➕ New Credit Note
            </a>
        </div>

        {{-- ================= TABLE ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
            <table class="min-w-full border-collapse text-sm">

                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                            Credit No
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                            Customer
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                            Issued Date
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                            Total
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                            Used
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                            Remaining
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                            Status
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse ($creditNotes as $credit)
                        <tr class="hover:bg-gray-50 cursor-pointer"
                            onclick="window.location='{{ route('staff.credit-notes.show', $credit) }}'">

                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $credit->credit_note_number }}
                            </td>

                            <td class="px-4 py-3 text-gray-900">
                                {{ $credit->customer->registered_company_name ?? $credit->customer->contact_name }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $credit->issued_at?->format('d M Y') ?? '—' }}
                            </td>

                            <td class="px-4 py-3 text-right font-semibold">
                                £{{ number_format($credit->total_amount, 2) }}
                            </td>

                            <td class="px-4 py-3 text-right text-green-700">
                                £{{ number_format($credit->total_allocated, 2) }}
                            </td>

                            <td class="px-4 py-3 text-right font-semibold">
                                £{{ number_format($credit->remaining_amount, 2) }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                @if ($credit->remaining_amount <= 0)
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                        USED
                                    </span>
                                @elseif ($credit->total_allocated > 0)
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">
                                        PARTIAL
                                    </span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">
                                        UNUSED
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                No credit notes found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <div class="mt-6">
            {{ $creditNotes->links() }}
        </div>

    </div>
@endsection
