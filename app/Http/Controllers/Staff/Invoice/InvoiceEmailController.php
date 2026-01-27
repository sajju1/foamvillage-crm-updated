<?php

namespace App\Http\Controllers\Staff\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice\Invoice;
use App\Models\Company\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class InvoiceEmailController extends Controller
{
    public function __invoke(Invoice $invoice)
    {
        $company = Company::getDefault();

        // Generate PDF from the SAME document used for download/print
        $pdf = Pdf::loadView('staff.invoices.document', [
            'invoice' => $invoice,
            'company' => $company,
        ]);

        // Send email with PDF attachment
        Mail::raw(
            "Please find attached invoice {$invoice->invoice_number}.",
            function ($message) use ($invoice, $pdf) {
                $message
                    ->to($invoice->customer->email)
                    ->subject("Invoice {$invoice->invoice_number}")
                    ->attachData(
                        $pdf->output(),
                        "invoice-{$invoice->invoice_number}.pdf",
                        ['mime' => 'application/pdf']
                    );
            }
        );

        return back()->with('success', 'Invoice emailed successfully.');
    }
}
