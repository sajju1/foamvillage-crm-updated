<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Portfolio Sheet</title>

    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .page {
            width: 100%;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .header-block {
            width: 48%;
        }

        .header h2 {
            margin: 0 0 6px 0;
            font-size: 15px;
        }

        .header p {
            margin: 2px 0;
        }

        .meta {
            margin-bottom: 15px;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
            word-wrap: break-word;
            word-break: break-word;
        }

        th {
            background-color: #f2f2f2;
            text-align: left;
        }

        .category-row {
            background-color: #e6e6e6;
            font-weight: bold;
        }

        .product-row {
            background-color: #f7f7f7;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .muted {
            color: #555;
        }
    </style>
</head>

<body>

    <div class="page">

        {{-- HEADER --}}
        <div class="header">

            {{-- CUSTOMER --}}
            <div class="header-block">
                <h2>Customer</h2>
                <p><strong>Account No:</strong> {{ $customerBlock['account_number'] }}</p>

                @if ($customerBlock['company_name'])
                    <p><strong>Company:</strong> {{ $customerBlock['company_name'] }}</p>
                @endif

                <p><strong>Contact:</strong> {{ $customerBlock['contact_name'] }}</p>

                @if ($customerBlock['phone'])
                    <p><strong>Phone:</strong> {{ $customerBlock['phone'] }}</p>
                @endif

                @if ($customerBlock['address'])
                    <p class="muted">{{ $customerBlock['address'] }}</p>
                @endif

            </div>

            {{-- COMPANY --}}
            <div class="header-block">
                <h2>Issued By</h2>
                <p><strong>{{ $companyBlock['name'] }}</strong></p>

                @if ($companyBlock['phone'])
                    <p>{{ $companyBlock['phone'] }}</p>
                @endif

                @if ($companyBlock['email'])
                    <p>{{ $companyBlock['email'] }}</p>
                @endif

                @if ($companyBlock['address'])
                    <p class="muted">{{ $companyBlock['address'] }}</p>
                @endif
            </div>
        </div>

        {{-- META --}}
        <div class="meta">
            <strong>Generated on:</strong> {{ $generatedDate }}
        </div>

        {{-- PORTFOLIO TABLE --}}
        <table>
            <thead>
                <tr>
                    <th style="width: 55%">Product</th>
                    <th style="width: 22.5%" class="text-right">Standard Price</th>
                    <th style="width: 22.5%" class="text-right">Customer Price</th>
                </tr>
            </thead>
            <tbody>

                @forelse($groupedPortfolio as $categoryName => $products)

                    <tr class="category-row">
                        <td colspan="3">{{ $categoryName }}</td>
                    </tr>

                    @foreach ($products as $productName => $rows)
                        <tr class="product-row">
                            <td colspan="3">{{ $productName }}</td>
                        </tr>

                        @foreach ($rows as $row)
                            <tr>
                                <td>{{ $row['label'] }}</td>
                                <td class="text-right">{{ $row['standard_price'] }}</td>
                                <td class="text-right">{{ $row['customer_price'] }}</td>
                            </tr>
                        @endforeach
                    @endforeach

                @empty
                    <tr>
                        <td colspan="3">No portfolio items found.</td>
                    </tr>
                @endforelse

            </tbody>
        </table>

    </div>

</body>

</html>
