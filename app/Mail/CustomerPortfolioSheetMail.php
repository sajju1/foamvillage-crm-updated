<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerPortfolioSheetMail extends Mailable
{
    use Queueable, SerializesModels;

    protected array $data;
    protected string $accountNumber;

    public function __construct(array $data, string $accountNumber)
    {
        $this->data = $data;
        $this->accountNumber = $accountNumber;
    }

    public function build()
    {
        $pdf = Pdf::loadView('customers.portfolio-sheet', $this->data)
            ->setPaper('a4');

        return $this->subject('Your Product Portfolio')
            ->view('emails.customer-portfolio-sheet', [
                'customerBlock' => $this->data['customerBlock'],
                'companyBlock'  => $this->data['companyBlock'],
            ])
            ->attachData(
                $pdf->output(),
                'customer-portfolio-' . $this->accountNumber . '.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
