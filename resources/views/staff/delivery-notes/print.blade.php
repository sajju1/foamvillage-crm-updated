<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery Note – {{ $deliveryNote->delivery_note_number }}</title>

    <style>
        @page {
            size: A4;
            margin: 18mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .company {
            font-size: 16px;
            font-weight: bold;
        }

        .doc-title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 4px;
        }

        .meta {
            text-align: right;
            font-size: 11px;
        }

        .meta div {
            margin-bottom: 4px;
        }

        .section {
            margin-bottom: 16px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead th {
            border-bottom: 2px solid #000;
            padding: 6px 8px;
            text-align: left;
        }

        tbody td {
            border-bottom: 1px solid #ccc;
            padding: 8px;
        }

        .qty {
            text-align: right;
            width: 80px;
        }

        .product-row td {
            background: #f3f3f3;
            font-weight: bold;
            border-top: 2px solid #000;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            margin-top: 40px;
        }

        .signature-line {
            width: 220px;
            border-bottom: 1px solid #000;
            margin-top: 6px;
        }
    </style>
</head>
<body onload="window.print()">

@php
    $groupedLines = $deliveryNote->lines->groupBy(
        fn ($line) => $line->orderLine->product_id
    );
@endphp

{{-- ================= HEADER ================= --}}
<div class="header">
    <div>
        <div class="company">FoamVillage</div>
        <div class="doc-title">
            {{ strtoupper($deliveryNote->type) }} NOTE
        </div>
    </div>

    <div class="meta">
        <div><strong>Note No:</strong> {{ $deliveryNote->delivery_note_number }}</div>
        <div><strong>Order No:</strong> {{ $deliveryNote->order->order_number }}</div>
        <div><strong>Date:</strong> {{ $deliveryNote->issued_at?->format('d M Y') }}</div>
    </div>
</div>

{{-- ================= CUSTOMER ================= --}}
<div class="section">
    <div class="section-title">Customer</div>
    <div>{{ $deliveryNote->customer->customer_name }}</div>
    <div>Account: {{ $deliveryNote->customer->account_number }}</div>
</div>

{{-- ================= ADDRESS ================= --}}
@if ($deliveryNote->address)
<div class="section">
    <div class="section-title">Delivery Address</div>
    <div>{{ $deliveryNote->address->address_line_1 }}</div>
    <div>{{ $deliveryNote->address->city }}</div>
</div>
@endif

{{-- ================= ITEMS ================= --}}
<table>
    <thead>
        <tr>
            <th>Product / Variation</th>
            <th class="qty">Requested</th>
            <th class="qty">Processed</th>
            <th class="qty">Pending</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($groupedLines as $lines)
            @php
                $product = $lines->first()->orderLine->product;
            @endphp

            <tr class="product-row">
                <td colspan="4">
                    {{ $product->product_name ?? 'Product' }}
                </td>
            </tr>

            @foreach ($lines as $line)
                @php
                    $requested = $line->orderLine->requested_quantity;
                    $processed = $line->processed_quantity;
                    $pending = max($requested - $processed, 0);
                @endphp

                <tr>
                    <td>
                        {{ $line->orderLine->productVariation?->display_name ?? '—' }}
                    </td>
                    <td class="qty">{{ $requested }}</td>
                    <td class="qty">{{ $processed }}</td>
                    <td class="qty">{{ $pending }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

{{-- ================= FOOTER ================= --}}
<div class="footer">
    <div>
        Printed on {{ now()->format('d M Y H:i') }}
    </div>

    <div class="signature">
        Processed by
        <div class="signature-line"></div>
    </div>
</div>

</body>
</html>
