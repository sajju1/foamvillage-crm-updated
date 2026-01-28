<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use Barryvdh\DomPDF\Facade\Pdf;


class CustomerStatementMail extends Mailable
{
    public function __construct(
        public $customer,
        public array $statement
    ) {}

    public function build()
    {
        $pdf = Pdf::loadView('staff.statements.pdf', [
            'customer'  => $this->customer,
            'statement' => $this->statement,
        ]);

        return $this->subject('Customer Statement')
            ->view('emails.statement-notification')
            ->attachData(
                $pdf->output(),
                'statement-' . $this->customer->account_number . '.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
