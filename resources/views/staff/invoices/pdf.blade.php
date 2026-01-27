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
            margin: 0;
            padding: 0;
        }

        .footer {
            position: fixed;
            bottom: -25mm;
            left: 18mm;
            right: 18mm;
            height: 25mm;
            font-size: 10px;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 6px;
        }
    </style>
</head>
<body>

<h2>{{ strtoupper($company->legal_name) }}</h2>

<table width="100%" style="margin-bottom:14px;">
    <tr>
        <td width="50%">
            <strong>Invoice To</strong><br>
            {{ $invoice->customer->registered_company_name
                ?: $invoice->customer->contact_name }}<br>
            Account: {{ $invoice->customer->account_number }}
        </td>
        <td width="50%" align="right">
            <strong>Invoice No:</strong> {{ $invoice->invoice_number }}<br>
            <strong>Date:</strong> {{ $invoice->issued_at->format('d M Y') }}
        </td>
    </tr>
</table>

<hr>

<table width="100%" cellpadding="6">
    <tr>
        <td>Invoice Total</td>
        <td align="right">
            £{{ number_format($invoice->total_amount, 2) }}
        </td>
    </tr>

    @if($invoice->total_paid > 0)
        <tr>
            <td>Payments Received</td>
            <td align="right">
                - £{{ number_format($invoice->total_paid, 2) }}
            </td>
        </tr>

        <tr>
            <td><strong>Balance Due</strong></td>
            <td align="right">
                <strong>
                    £{{ number_format($invoice->balance_due, 2) }}
                </strong>
            </td>
        </tr>
    @endif
</table>

<div class="footer">
    {{ $company->address_line1 }},
    {{ $company->city }},
    {{ $company->postcode }},
    {{ $company->country }}<br>
    Email: {{ $company->email }} | Phone: {{ $company->phone }}
</div>

</body>
</html>
