<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>INVOICE – {{ $invoice->invoice_number }}</title>

    <style>
        @page {
            size: A4;
            margin: 18mm 18mm 35mm 18mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* ================= WATERMARK ================= */
        .watermark {
            position: fixed;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 90px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.08);
            z-index: -1;
            letter-spacing: 10px;
            user-select: none;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .company-meta {
            font-size: 10.5px;
            margin-bottom: 12px;
        }

        .layout td {
            vertical-align: top;
        }

        .center-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        .right-meta {
            text-align: right;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 4px;
        }

        /* ================= ITEMS TABLE ================= */

        table.items {
            margin-top: 14px;
            table-layout: fixed;
        }

        table.items th,
        table.items td {
            padding: 7px 4px;
            font-size: 11px;
        }

        table.items th {
            border-bottom: 2px solid #000;
            font-weight: bold;
        }

        table.items td {
            border-bottom: 1px solid #ccc;
        }

        .num {
            text-align: right;
            white-space: nowrap;
        }

        tr {
            page-break-inside: avoid;
        }

        /* ================= TOTALS ================= */

        .totals {
            margin-top: 16px;
            width: 100%;
        }

        .totals td {
            padding: 5px 4px;
        }

        .totals .label {
            text-align: right;
        }

        .totals .value {
            text-align: right;
            font-weight: bold;
            width: 120px;
        }

        .grand-total {
            border-top: 2px solid #000;
            font-size: 13px;
        }

        /* ================= PAYMENT ================= */

        .payment-details {
            margin-top: 18px;
            font-size: 10.5px;
        }

        /* ================= FOOTER ================= */

        .footer {
            position: fixed;
            bottom: -25mm;
            left: 18mm;
            right: 18mm;
            height: 25mm;
            font-size: 10.5px;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 6px;
        }
    </style>
</head>

<body>

    {{-- ================= PAID WATERMARK ================= --}}
    @if ($invoice->balance_due <= 0)
        <div class="watermark">PAID</div>
    @endif

    @php
        use Illuminate\Support\Str;

        $customer = $invoice->customer;
        $billing = $customer->billingAddress;
        $customerName = $customer->registered_company_name ?: $customer->contact_name;
    @endphp

    {{-- ================= SELLER ================= --}}
    <div class="company-name">
        {{ Str::title($company->legal_name) }}
    </div>

    <div class="company-meta">
        @if ($company->company_number)
            Company No: {{ $company->company_number }}<br>
        @endif
        VAT No: {{ $company->vat_number }}
    </div>

    {{-- ================= META ROW ================= --}}
    <table class="layout">
        <tr>
            <td style="width:33%">
                <div class="section-title">BILL TO</div>

                <div>Account: {{ $customer->account_number }}</div>
                <div>{{ Str::title($customerName) }}</div>
                <div>
                    @if ($billing)
                        {{ $billing->address_line1 }}<br>

                        @if ($billing->address_line2)
                            {{ $billing->address_line2 }}<br>
                        @endif

                        {{ $billing->city }}<br>
                        {{ $billing->postcode }}<br>
                        {{ $billing->country }}
                    @else
                        <span style="color:#999">
                            Billing address not set
                        </span>
                    @endif
                </div>
            </td>

            <td style="width:34%" class="center-title">
                INVOICE
            </td>

            <td style="width:33%" class="right-meta">
                <div><strong>Invoice No:</strong> {{ $invoice->invoice_number }}</div>
                <div><strong>Invoice Date:</strong> {{ $invoice->issued_at?->format('d M Y') }}</div>
                <div><strong>Due Date:</strong> {{ $invoice->due_date?->format('d M Y') ?? '—' }}</div>


                @if ($invoice->deliveryNote)
                    <div>
                        <strong>Delivery Note:</strong>
                        {{ $invoice->deliveryNote->delivery_note_number }}
                    </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- ================= ITEMS ================= --}}
    <table class="items">
        <colgroup>
            <col style="width:40%">
            <col style="width:10%">
            <col style="width:15%">
            <col style="width:10%">
            <col style="width:12%">
            <col style="width:13%">
        </colgroup>

        <thead>
            <tr>
                <th>Description</th>
                <th class="num">Qty</th>
                <th class="num">Unit (ex VAT)</th>
                <th class="num">VAT %</th>
                <th class="num">VAT</th>
                <th class="num">Total</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($invoice->lines as $line)
                <tr>
                    <td>
                        {{ $line->description }}

                        @if ($line->note)
                            <div style="font-size:10px; color:#555; margin-top:2px;">
                                {{ $line->note }}
                            </div>
                        @endif
                    </td>
                    <td class="num">{{ number_format($line->quantity, 2) }}</td>
                    <td class="num">£{{ number_format($line->unit_price_ex_vat, 2) }}</td>
                    <td class="num">{{ number_format($line->vat_rate, 2) }}%</td>
                    <td class="num">£{{ number_format($line->vat_amount, 2) }}</td>
                    <td class="num">£{{ number_format($line->line_total_inc_vat, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ================= TOTALS ================= --}}
    <table class="totals">
        <tr>
            <td class="label">Subtotal</td>
            <td class="value">£{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="label">VAT</td>
            <td class="value">£{{ number_format($invoice->vat_amount, 2) }}</td>
        </tr>

        <tr class="grand-total">
            <td class="label">Invoice Total</td>
            <td class="value">£{{ number_format($invoice->total_amount, 2) }}</td>
        </tr>

        @if ($invoice->total_paid > 0)
            <tr>
                <td class="label">Payments Received</td>
                <td class="value">- £{{ number_format($invoice->total_paid, 2) }}</td>
            </tr>

            <tr class="grand-total">
                <td class="label">Balance Due</td>
                <td class="value">£{{ number_format($invoice->balance_due, 2) }}</td>
            </tr>
        @endif
    </table>

    {{-- ================= PAYMENT DETAILS ================= --}}
    <div class="payment-details">
        <div class="section-title">Payment Details</div>
        <div>Bank: {{ $company->bank_name }}</div>
        <div>Account Name: {{ $company->bank_account_name }}</div>
        <div>Account Number: {{ $company->bank_account_number }}</div>
        <div>Sort Code: {{ $company->bank_sort_code }}</div>

        @if ($company->bank_iban)
            <div>IBAN: {{ $company->bank_iban }}</div>
        @endif

        @if ($company->bank_swift_bic)
            <div>BIC / SWIFT: {{ $company->bank_swift_bic }}</div>
        @endif
    </div>

    {{-- ================= FOOTER ================= --}}
    <div class="footer">
        {{ $company->address_line1 }},
        {{ $company->city }},
        {{ $company->postcode }},
        {{ $company->country }}<br>
        Email: {{ $company->email }} | Phone: {{ $company->phone }}
    </div>

</body>

</html>
