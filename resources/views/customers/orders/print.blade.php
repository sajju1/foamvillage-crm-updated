<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Sheet – {{ $order->order_number }}</title>

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

        h1, h2, h3 {
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        thead th {
            border-bottom: 2px solid #000;
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
        }

        tbody td {
            border-bottom: 1px solid #ccc;
            padding: 10px 8px;
            vertical-align: top;
        }

        .product-row td {
            background: #f3f3f3;
            font-weight: bold;
            border-top: 2px solid #000;
        }

        .qty {
            text-align: right;
            width: 80px;
        }

        .processed {
            width: 110px;
            border-bottom: 1px solid #000;
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
            width: 200px;
            border-bottom: 1px solid #000;
            margin-top: 6px;
        }
    </style>
</head>
<body onload="window.print()">

@php
    $groupedLines = $order->orderLines->groupBy('product_id');
@endphp

{{-- ================= HEADER ================= --}}
<div class="header">
    <div>
        <div class="company">FoamVillage</div>
        <div class="doc-title">ORDER SHEET</div>
    </div>

    <div class="meta">
        <div><strong>Order No:</strong> {{ $order->order_number }}</div>
        <div><strong>Customer:</strong> {{ $order->customer->account_number ?? '—' }}</div>
        <div><strong>Date:</strong> {{ optional($order->submitted_at)->format('d M Y') }}</div>
    </div>
</div>

{{-- ================= TABLE ================= --}}
<table>
    <thead>
        <tr>
            <th>Product / Variation</th>
            <th class="qty">Requested</th>
            <th class="qty">Processed</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($groupedLines as $lines)
            @php
                $product = $lines->first()->product;
            @endphp

            {{-- PRODUCT HEADER --}}
            <tr class="product-row">
                <td colspan="3">
                    {{ $product->product_name ?? 'Product' }}
                </td>
            </tr>

            {{-- VARIATIONS --}}
            @foreach ($lines as $line)
                <tr>
                    <td>
                        {{ $line->productVariation?->display_name ?? '—' }}
                    </td>
                    <td class="qty">
                        {{ $line->requested_quantity }}
                    </td>
                    <td class="processed"></td>
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
        Picked / Processed by
        <div class="signature-line"></div>
    </div>
</div>

</body>
</html>
