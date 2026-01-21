<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Sheet – {{ $order->order_number }}</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        h1 {
            font-size: 18px;
            margin-bottom: 4px;
        }
        .meta {
            margin-bottom: 20px;
        }
        .meta div {
            margin-bottom: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background: #f2f2f2;
        }
        .right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            font-size: 11px;
        }
    </style>
</head>
<body>

<div class="container">

    <h1>Order Sheet</h1>

    <div class="meta">
        <div><strong>Order No:</strong> {{ $order->order_number }}</div>
        <div><strong>Status:</strong> {{ ucfirst(str_replace('_',' ', $order->status)) }}</div>
        <div><strong>Customer Code:</strong> {{ $order->customer->account_number ?? $order->customer->id }}</div>
        <div><strong>Created:</strong> {{ $order->created_at?->format('d M Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Variation</th>
                <th class="right">Requested</th>
                <th class="right">Processed</th>
                <th class="right">Pending</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->lines->where('line_status','active') as $line)
                <tr>
                    <td>{{ $line->product->name ?? 'Product #' . $line->product_id }}</td>
                    <td>{{ $line->variation->name ?? '—' }}</td>
                    <td class="right">{{ $line->requested_quantity }}</td>
                    <td class="right">{{ $line->processed_quantity }}</td>
                    <td class="right">{{ $line->pendingQuantity() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div>This document is system generated.</div>
        <div>No prices or totals are shown.</div>
    </div>

</div>

</body>
</html>
