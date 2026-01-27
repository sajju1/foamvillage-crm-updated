@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-8">

        {{-- ================= HEADER ================= --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Edit Invoice</h1>
                <p class="text-sm text-gray-500">{{ $invoice->invoice_number }}</p>
                <p class="mt-1 text-xs text-red-600">
                    You are editing a financial document. Changes will affect totals.
                </p>
            </div>

            <div class="flex gap-2 print:hidden items-center">
                <a href="{{ route('staff.invoices.show', $invoice) }}" class="crm-btn-secondary">
                    ← Back to Invoice
                </a>
            </div>
        </div>
        {{-- ================= INVOICE DATES ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
            <form method="POST" action="{{ route('staff.invoices.update-due-date', $invoice) }}"
                class="px-6 py-4 flex flex-wrap items-end gap-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs text-gray-500 mb-1">
                        Due Date
                    </label>
                    <input type="date" name="due_date" value="{{ $invoice->due_date?->format('Y-m-d') }}" required
                        class="border border-gray-300 rounded px-3 py-2 text-sm">
                </div>

                <div class="pt-4">
                    <button type="submit" class="crm-btn-primary text-sm"
                        onclick="return confirm('Update invoice due date?')">
                        Update Due Date
                    </button>
                </div>
            </form>
        </div>


        {{-- ================= EXISTING INVOICE LINES ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6 overflow-x-auto">
            <table class="min-w-full border-collapse text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Description</th>
                        <th class="px-4 py-3 text-right">Qty</th>
                        <th class="px-4 py-3 text-right">Unit (ex VAT)</th>
                        <th class="px-4 py-3 text-right">VAT %</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($invoice->lines as $line)
                        <tr>
                            <td class="px-4 py-3 align-top">
                                <div class="font-medium text-gray-900">
                                    {{ $line->description }}
                                </div>

                                {{-- Edit note --}}
                                <form method="POST" action="{{ route('staff.invoices.lines.note', $line) }}"
                                    class="mt-2">
                                    @csrf
                                    @method('PATCH')
                                    <textarea name="note" rows="2" class="w-full border border-gray-300 rounded px-2 py-1 text-sm"
                                        placeholder="Add note">{{ old('note', $line->note) }}</textarea>

                                    <div class="mt-1 text-right">
                                        <button type="submit" class="crm-btn-secondary text-xs">
                                            Save note
                                        </button>
                                    </div>
                                </form>
                            </td>

                            <td class="px-4 py-3 text-right">
                                {{ number_format($line->quantity, 2) }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                £{{ number_format($line->unit_price_ex_vat, 2) }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                {{ number_format($line->vat_rate, 2) }}%
                            </td>

                            <td class="px-4 py-3 text-right font-medium">
                                £{{ number_format($line->line_total_inc_vat, 2) }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('staff.invoices.lines.destroy', $line) }}"
                                    onsubmit="return confirm('Remove this line from invoice?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ================= ADD INVOICE LINE ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
            <div class="px-6 py-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">
                    Add Invoice Item
                </h3>

                @if ($invoice->customer->is_account_customer)
                    <p class="text-xs text-gray-500 mb-3">
                        Pricing will be taken from customer portfolio and cannot be edited.
                    </p>
                @else
                    <p class="text-xs text-gray-500 mb-3">
                        Default price will be used. You may override before saving.
                    </p>
                @endif

                <form method="POST" action="{{ route('staff.invoices.lines.store', $invoice) }}">
                    @csrf

                    {{-- SEARCH --}}
                    <label class="block text-xs text-gray-500 mb-1">Search product</label>
                    <input type="text" id="product-search" class="w-full border rounded px-3 py-2 text-sm"
                        placeholder="Start typing product name…" autocomplete="off">

                    <input type="hidden" name="portfolio_id" id="portfolio-id">
                    <input type="hidden" name="product_variation_id" id="variation-id">

                    <div id="search-results"
                        class="border border-gray-200 rounded mt-1 max-h-48 overflow-y-auto hidden bg-white text-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Quantity</label>
                            <input type="number" name="quantity" step="0.01" min="0.01" required
                                class="w-full border rounded px-2 py-2 text-sm">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Note (optional)</label>
                            <input type="text" name="note" class="w-full border rounded px-2 py-2 text-sm">
                        </div>

                        <div class="flex items-end justify-end">
                            <button type="submit" class="crm-btn-primary">
                                ➕ Add Item
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= TOTALS (REFERENCE ONLY) ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <table class="min-w-full border-collapse text-sm">
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 text-gray-600">Current Invoice Total</td>
                        <td class="px-6 py-4 text-right font-semibold">
                            £{{ number_format($invoice->total_amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
    <script>
        const searchInput = document.getElementById('product-search');
        const resultsBox = document.getElementById('search-results');
        const portfolioIdInput = document.getElementById('portfolio-id');
        const variationIdInput = document.getElementById('variation-id');

        let controller;

        searchInput?.addEventListener('input', function() {
            const q = this.value.trim();

            if (q.length < 2) {
                resultsBox.classList.add('hidden');
                resultsBox.innerHTML = '';
                portfolioIdInput.value = '';
                variationIdInput.value = '';
                return;
            }

            if (controller) controller.abort();
            controller = new AbortController();

            const isAccountCustomer = {{ $invoice->customer->is_account_customer ? 'true' : 'false' }};

            const url = isAccountCustomer ?
                `{{ route('staff.invoices.search.portfolio', $invoice->customer_id) }}?q=${encodeURIComponent(q)}` :
                `{{ route('staff.invoices.search.products') }}?q=${encodeURIComponent(q)}`;

            fetch(url, {
                    signal: controller.signal
                })
                .then(r => {
                    if (!r.ok) throw new Error('Search failed');
                    return r.json();
                })
                .then(data => {
                    resultsBox.innerHTML = '';

                    if (!data.length) {
                        resultsBox.classList.add('hidden');
                        return;
                    }

                    resultsBox.classList.remove('hidden');

                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer';
                        div.textContent = item.label;

                        div.onclick = () => {
                            searchInput.value = item.label;
                            resultsBox.classList.add('hidden');

                            if (isAccountCustomer) {
                                portfolioIdInput.value = item.id;
                                variationIdInput.value = '';
                            } else {
                                variationIdInput.value = item.variation_id ?? item.id;
                                portfolioIdInput.value = '';
                            }
                        };

                        resultsBox.appendChild(div);
                    });
                })
                .catch(() => {
                    resultsBox.classList.add('hidden');
                });
        });
    </script>
@endsection
