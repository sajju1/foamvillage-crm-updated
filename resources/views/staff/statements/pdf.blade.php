<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Statement</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }

        /* ===== HEADER ===== */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 18px;
        }

        .header-left,
        .header-right {
            display: table-cell;
            vertical-align: top;
        }

        .header-left {
            width: 60%;
        }

        .header-right {
            width: 40%;
            text-align: right;
        }

        h1 {
            font-size: 20px;
            margin: 0 0 6px 0;
        }

        .customer {
            font-size: 14px;
            font-weight: bold;
        }

        .account {
            font-size: 12px;
            color: #555;
            margin-top: 2px;
        }

        .duration {
            font-size: 13px;
            margin-top: 10px;
            font-weight: bold;
        }

        /* ===== SUMMARY BOX ===== */
        .summary-box {
            border: 1px solid #ddd;
            padding: 10px 12px;
            border-radius: 4px;
        }

        .summary-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-box td {
            padding: 4px 0;
            font-size: 12px;
        }

        .summary-box .label {
            color: #555;
        }

        .summary-box .value {
            font-weight: bold;
        }

        /* ===== LEDGER ===== */
        table.ledger {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        table.ledger th,
        table.ledger td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }

        table.ledger th {
            background: #f3f4f6;
            font-size: 11px;
            text-transform: uppercase;
            text-align: left;
        }

        .right {
            text-align: right;
        }
    </style>
</head>
<body>

{{-- ================= HEADER ================= --}}
<div class="header">
    <div class="header-left">
        <h1>Customer Statement</h1>

        <div class="customer">
            {{ strtoupper($customer->registered_company_name ?: $customer->contact_name) }}
        </div>

        <div class="account">
            Account No: {{ $customer->account_number }}
        </div>

        @if($statement['from'] || $statement['to'])
            <div class="duration">
                Duration:
                {{ $statement['from']?->format('d M Y') ?? '—' }}
                →
                {{ $statement['to']?->format('d M Y') ?? '—' }}
            </div>
        @endif
    </div>

    <div class="header-right">
        <div class="summary-box">
            <table>
                <tr>
                    <td class="label">Opening</td>
                    <td class="value right">
                        £{{ number_format($statement['summary']['opening_balance'], 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Invoiced</td>
                    <td class="value right">
                        £{{ number_format($statement['summary']['total_invoiced'], 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Paid</td>
                    <td class="value right">
                        £{{ number_format($statement['summary']['total_paid'], 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Credits</td>
                    <td class="value right">
                        £{{ number_format($statement['summary']['total_credits'], 2) }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

{{-- ================= LEDGER ================= --}}
<table class="ledger">
    <thead>
        <tr>
            <th>Date</th>
            <th>Reference</th>
            <th>Description</th>
            <th class="right">Debit</th>
            <th class="right">Credit</th>
            <th class="right">Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($statement['rows'] as $row)
            <tr>
                <td>{{ $row['date']->format('d M Y') }}</td>
                <td>{{ $row['reference'] }}</td>
                <td>{{ $row['description'] }}</td>
                <td class="right">
                    {{ $row['debit'] > 0 ? '£'.number_format($row['debit'],2) : '' }}
                </td>
                <td class="right">
                    {{ $row['credit'] > 0 ? '£'.number_format($row['credit'],2) : '' }}
                </td>
                <td class="right">
                    £{{ number_format($row['balance'],2) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
