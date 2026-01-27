@php
    $customerName = $invoice->customer->registered_company_name
        ?: $invoice->customer->contact_name;
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Reminder – Invoice {{ $invoice->invoice_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">

    <p>Dear {{ $customerName }},</p>

    <p>
        We hope you are well.
    </p>

    <p>
        This is a friendly reminder regarding the outstanding invoice
        <strong>{{ $invoice->invoice_number }}</strong>
        issued on {{ $invoice->issued_at->format('d M Y') }}.
    </p>

    <p>
        <strong>Invoice total:</strong>
        £{{ number_format($invoice->total_amount, 2) }}<br>

        <strong>Amount outstanding:</strong>
        £{{ number_format($invoice->balance_due, 2) }}<br>

        <strong>Due date:</strong>
        {{ $invoice->due_date?->format('d M Y') ?? '—' }}
    </p>

    @if ($invoice->due_date && $invoice->due_date->isPast())
        <p style="color: #b91c1c;">
            This invoice is now overdue.
        </p>
    @endif

    <p>
        If you have already arranged payment, please disregard this message.
        Otherwise, we would appreciate your assistance in settling the balance
        at your earliest convenience.
    </p>

    <p>
        If you have any questions or require a copy of the invoice,
        please feel free to contact us.
    </p>

    <p>
        Kind regards,<br>
        <strong>{{ config('app.name') }}</strong>
    </p>

</body>
</html>
