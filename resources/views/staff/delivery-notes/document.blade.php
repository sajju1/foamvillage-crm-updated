<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ strtoupper($deliveryNote->type) }} NOTE – {{ $deliveryNote->delivery_note_number }}</title>

    <style>
        @page {
            size: A4;
            margin: 18mm 18mm 35mm 18mm; /* bottom margin RESERVED for footer */
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .company-header {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 14px;
        }

        table.layout {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        table.layout td {
            vertical-align: top;
        }

        .center-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        .right-meta {
            text-align: right;
            font-size: 11px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 4px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: auto;
        }

        table.items th {
            text-align: left;
            border-bottom: 2px solid #000;
            padding: 6px 0;
        }

        table.items td {
            border-bottom: 1px solid #ccc;
            padding: 8px 0;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .qty {
            text-align: right;
            width: 140px;
        }

        .product-row td {
            font-weight: bold;
            padding-top: 12px;
        }

        .signature {
            margin-top: 30px;
            text-align: right;
        }

        .signature-line {
            display: inline-block;
            width: 220px;
            border-bottom: 1px solid #000;
            margin-left: 8px;
        }

        /* ================= STICKY FOOTER ================= */
        .footer {
            position: fixed;
            bottom: -25mm; /* pulls footer into reserved margin */
            left: 18mm;
            right: 18mm;
            height: 25mm;

            font-size: 10.5px;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 6px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

@php
    use Illuminate\Support\Str;

    $customer = $deliveryNote->customer;

    $customerDisplayName = $customer->registered_company_name
        ?: $customer->contact_name;

    $groupedLines = $deliveryNote->lines->groupBy(
        fn ($line) => $line->orderLine->product_id
    );

    $hasPending = false;
    foreach ($deliveryNote->lines as $line) {
        if ($line->orderLine->requested_quantity > $line->processed_quantity) {
            $hasPending = true;
            break;
        }
    }
@endphp

{{-- ================= HEADER ================= --}}
<table style="width:100%; margin-bottom:14px;">
    <tr>
        <td style="font-size:16px; font-weight:bold;">
            {{ Str::title($company->legal_name) }}
        </td>
    </tr>
</table>

{{-- ================= META ROW ================= --}}
<table class="layout">
    <tr>
        <td style="width:33%">
            <div class="section-title">CUSTOMER</div>
            <div>Account: {{ $customer->account_number }}</div>
            <div>{{ Str::title($customerDisplayName) }}</div>
            <br>
            <div>{{ Str::title($deliveryNote->address->address_line1) }}</div>
            @if($deliveryNote->address->address_line2)
                <div>{{ Str::title($deliveryNote->address->address_line2) }}</div>
            @endif
            <div>{{ Str::title($deliveryNote->address->city) }}</div>
            <div>{{ $deliveryNote->address->postcode }}</div>
            <div>{{ Str::title($deliveryNote->address->country) }}</div>
        </td>

        <td style="width:34%" class="center-title">
            {{ strtoupper($deliveryNote->type) }} NOTE
        </td>

        <td style="width:33%" class="right-meta">
            <div><strong>Date:</strong> {{ $deliveryNote->issued_at?->format('d M Y') }}</div>
            <div><strong>Order No:</strong> {{ $deliveryNote->order->order_number }}</div>
            <div><strong>Note No:</strong> {{ $deliveryNote->delivery_note_number }}</div>
        </td>
    </tr>
</table>

{{-- ================= ITEMS TABLE ================= --}}
<table class="items">
    <thead>
        <tr>
            <th>Product / Variation</th>
            <th class="qty">Delivered Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($groupedLines as $lines)
            @php $product = $lines->first()->orderLine->product; @endphp

            <tr class="product-row">
                <td colspan="2">{{ Str::title($product->product_name) }}</td>
            </tr>

            @foreach ($lines as $line)
                @if($line->processed_quantity > 0)
                    <tr>
                        <td>{{ $line->orderLine->productVariation?->display_name }}</td>
                        <td class="qty">{{ $line->processed_quantity }}</td>
                    </tr>
                @endif
            @endforeach
        @endforeach
    </tbody>
</table>

{{-- ================= SIGNATURE ================= --}}
<div class="signature">
    Processed by <span class="signature-line"></span>
</div>

{{-- ================= FOOTER (STICKY) ================= --}}
<div class="footer">
    {{ Str::title($company->address_line1) }},
    {{ Str::title($company->city) }},
    {{ $company->postcode }},
    {{ Str::title($company->country) }}<br>
    Email: {{ $company->email }} | Phone: {{ $company->phone }}
</div>

{{-- ================= PENDING ITEMS (CONDITIONAL) ================= --}}
@if($hasPending)
    <div class="page-break"></div>

    <table class="items">
        <thead>
            <tr>
                <th>Product / Variation</th>
                <th class="qty">Pending Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($groupedLines as $lines)
                @foreach ($lines as $line)
                    @php
                        $pending = max(
                            $line->orderLine->requested_quantity - $line->processed_quantity,
                            0
                        );
                    @endphp

                    @if($pending > 0)
                        <tr>
                            <td>
                                {{ Str::title($line->orderLine->product->product_name) }} —
                                {{ $line->orderLine->productVariation?->display_name }}
                            </td>
                            <td class="qty">{{ $pending }}</td>
                        </tr>
                    @endif
                @endforeach
            @endforeach
        </tbody>
    </table>
@endif

</body>
</html>
