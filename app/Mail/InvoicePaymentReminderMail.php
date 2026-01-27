<?php

namespace App\Mail;

use App\Models\Invoice\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoicePaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invoice $invoice;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->subject('Payment Reminder – Invoice ' . $this->invoice->invoice_number)
            ->view('staff.invoices.emails.reminder')
            ->with([
                'invoice' => $this->invoice,
            ]);
    }
}
