<?php

namespace App\Http\Controllers\Staff\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Company\Company;


class InvoicePdfController extends Controller
{
    public function show(Invoice $invoice)
    {
        $company = Company::first(); // same as delivery note PDF

        return Pdf::loadView('staff.invoices.document', [
            'invoice' => $invoice,
            'company' => $company,
        ])->stream("invoice-{$invoice->invoice_number}.pdf");
    }

    public function download(Invoice $invoice)
    {
        $company = Company::first();

        return Pdf::loadView('staff.invoices.document', [
            'invoice' => $invoice,
            'company' => $company,
        ])->download("invoice-{$invoice->invoice_number}.pdf");
    }
}
