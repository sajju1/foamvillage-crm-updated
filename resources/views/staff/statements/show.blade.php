@extends('layouts.app')

@section('content')
    @php
        $summary = $statement['summary'];
        $rows = $statement['rows'];
    @endphp
    <div class="max-w-7xl mx-auto px-6 py-8 space-y-6">

        {{-- ================= HEADER ================= --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    Customer Statement
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    {{ strtoupper($customer->registered_company_name ?: $customer->contact_name) }}
                    <span class="text-gray-400">•</span>
                    {{ $customer->account_number }}
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('staff.statements.index') }}" class="crm-btn-secondary">
                    Change Customer
                </a>

                {{-- Download / Print BOTH go to PDF --}}
                <a href="{{ route('staff.statements.pdf', $customer) }}?from={{ request('from') }}&to={{ request('to') }}"
                    class="crm-btn-primary">
                    Download PDF
                </a>

                <a href="{{ route('staff.statements.pdf', $customer) }}?from={{ request('from') }}&to={{ request('to') }}"
                    target="_blank" class="crm-btn-secondary">
                    Print
                </a>
                <form method="POST"
                    action="{{ route('staff.statements.email', $customer) }}?from={{ request('from') }}&to={{ request('to') }}">
                    @csrf
                    <button class="crm-btn-secondary">
                        Email PDF
                    </button>
                </form>

            </div>
        </div>

        {{-- ================= FILTER BAR ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">
            <form method="GET" id="statementFilterForm" class="flex flex-wrap items-end gap-4">

                {{-- Period --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">
                        Period
                    </label>
                    <select id="period" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-44"
                        onchange="applyPeriod(this.value)">
                        <option value="">Select…</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_year">This Year</option>
                        <option value="last_year">Last Year</option>
                        <option value="custom" {{ request('from') || request('to') ? 'selected' : '' }}>
                            Custom
                        </option>
                    </select>
                </div>

                {{-- From --}}
                <div id="fromWrap" class="{{ request('from') || request('to') ? '' : 'hidden' }}">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">
                        From
                    </label>
                    <input type="date" name="from" id="from" value="{{ request('from') }}"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>

                {{-- To --}}
                <div id="toWrap" class="{{ request('from') || request('to') ? '' : 'hidden' }}">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">
                        To
                    </label>
                    <input type="date" name="to" id="to" value="{{ request('to') }}"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>

                <button class="crm-btn-primary">
                    View Statement
                </button>

                @if (request('from') || request('to'))
                    <a href="{{ route('staff.statements.show', $customer) }}" class="crm-btn-secondary">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- ================= SUMMARY ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white border rounded-lg p-4">
                <div class="text-xs uppercase font-semibold text-gray-500">Opening</div>
                <div class="text-xl font-bold">£{{ number_format($summary['opening_balance'], 2) }}</div>
            </div>

            <div class="bg-white border rounded-lg p-4">
                <div class="text-xs uppercase font-semibold text-gray-500">Invoiced</div>
                <div class="text-xl font-bold">£{{ number_format($summary['total_invoiced'], 2) }}</div>
            </div>

            <div class="bg-white border rounded-lg p-4">
                <div class="text-xs uppercase font-semibold text-gray-500">Paid</div>
                <div class="text-xl font-bold text-green-700">£{{ number_format($summary['total_paid'], 2) }}</div>
            </div>

            <div class="bg-white border rounded-lg p-4">
                <div class="text-xs uppercase font-semibold text-gray-500">Credits</div>
                <div class="text-xl font-bold text-emerald-700">£{{ number_format($summary['total_credits'], 2) }}</div>
            </div>

            <div class="bg-white border rounded-lg p-4">
                <div class="text-xs uppercase font-semibold text-gray-500">Closing</div>
                <div class="text-xl font-bold text-red-700">£{{ number_format($summary['closing_balance'], 2) }}</div>
            </div>
        </div>

        {{-- ================= LEDGER ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Debit</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Credit</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Balance</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @foreach ($rows as $row)
                        <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50">
                            <td class="px-6 py-3">{{ $row['date']->format('d M Y') }}</td>

                            <td class="px-6 py-3 font-medium">
                                @if ($row['link'])
                                    <a href="{{ $row['link'] }}" class="text-blue-600 hover:underline">
                                        {{ $row['reference'] }}
                                    </a>
                                @else
                                    {{ $row['reference'] }}
                                @endif
                            </td>

                            <td class="px-6 py-3">{{ $row['description'] }}</td>

                            <td class="px-6 py-3 text-right">
                                @if ($row['debit'] > 0)
                                    £{{ number_format($row['debit'], 2) }}
                                @endif
                            </td>

                            <td class="px-6 py-3 text-right text-green-700">
                                @if ($row['credit'] > 0)
                                    £{{ number_format($row['credit'], 2) }}
                                @endif
                            </td>

                            <td class="px-6 py-3 text-right font-bold">
                                £{{ number_format($row['balance'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ================= GRAPH ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 h-64">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Balance Over Time
            </h3>
            <canvas id="balanceChart" height="120"></canvas>
        </div>

    </div>

    {{-- ================= JS ================= --}}
    <script>
        function applyPeriod(period) {
            const from = document.getElementById('from');
            const to = document.getElementById('to');
            const fromWrap = document.getElementById('fromWrap');
            const toWrap = document.getElementById('toWrap');

            const today = new Date();
            let start, end;

            switch (period) {
                case 'this_month':
                    start = new Date(today.getFullYear(), today.getMonth(), 1);
                    end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    break;
                case 'last_month':
                    start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    end = new Date(today.getFullYear(), today.getMonth(), 0);
                    break;
                case 'this_year':
                    start = new Date(today.getFullYear(), 0, 1);
                    end = new Date(today.getFullYear(), 11, 31);
                    break;
                case 'last_year':
                    start = new Date(today.getFullYear() - 1, 0, 1);
                    end = new Date(today.getFullYear() - 1, 11, 31);
                    break;
                case 'custom':
                    fromWrap.classList.remove('hidden');
                    toWrap.classList.remove('hidden');
                    return;
                default:
                    return;
            }

            from.value = start.toISOString().slice(0, 10);
            to.value = end.toISOString().slice(0, 10);

            document.getElementById('statementFilterForm').submit();
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dataPoints = @json($statement['graph_points'] ?? []);

            if (!dataPoints.length) {
                return;
            }

            const labels = dataPoints.map(p => p.date);
            const balances = dataPoints.map(p => p.balance);

            const ctx = document.getElementById('balanceChart');
            if (!ctx) return;

            new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Outstanding Balance (£)',
                        data: balances,
                        borderWidth: 2,
                        tension: 0.3,
                        pointRadius: 3,
                        borderColor: '#2563eb', // blue-600
                        backgroundColor: 'rgba(37, 99, 235, 0.05)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '£' + context.parsed.y.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: value => '£' + value.toLocaleString()
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
