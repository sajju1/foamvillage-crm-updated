<?php

namespace App\Http\Controllers\Staff\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Delivery\DeliveryNote;
use App\Models\Invoice\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Invoice\InvoiceLineFactory;
use Carbon\Carbon;

class ConvertDeliveryNoteToInvoiceController extends Controller
{
    /**
     * Convert a delivery / collection note into an invoice.
     */
    public function __invoke(Request $request, DeliveryNote $deliveryNote): RedirectResponse
    {
        // 🔒 Guard: already invoiced
        if ($deliveryNote->invoice_id) {
            return redirect()
                ->back()
                ->with('error', 'This delivery note has already been invoiced.');
        }

        // ================= VALIDATE DUE DATE INPUT =================
        $validated = $request->validate([
            'payment_terms' => ['required', 'in:due_on_receipt,7_days,14_days,30_days,custom'],
            'custom_due_date' => ['required_if:payment_terms,custom', 'date'],
        ]);

        // ================= COMPUTE DUE DATE =================
        $issuedAt = now();

        $dueDate = match ($validated['payment_terms']) {
            'due_on_receipt' => $issuedAt,
            '7_days'         => $issuedAt->copy()->addDays(7),
            '14_days'        => $issuedAt->copy()->addDays(14),
            '30_days'        => $issuedAt->copy()->addDays(30),
            'custom'         => Carbon::parse($validated['custom_due_date']),
        };

        // Load everything needed (avoid N+1 and missing relations)
        $deliveryNote->load(
            'lines.orderLine.product',
            'lines.orderLine.productVariation'
        );

        DB::transaction(function () use ($deliveryNote, $issuedAt, $dueDate) {

            // ================= CREATE INVOICE =================
            $invoice = Invoice::create([
                'invoice_number'   => $this->generateInvoiceNumber(),
                'customer_id'      => $deliveryNote->customer_id,
                'delivery_note_id' => $deliveryNote->id,
                'issued_at'        => $issuedAt,
                'due_date'         => $dueDate,
                'subtotal'         => 0.00,
                'vat_amount'       => 0.00,
                'total_amount'     => 0.00,
                'currency'         => 'GBP',
            ]);

            // ================= CREATE INVOICE LINES =================
            foreach ($deliveryNote->lines as $deliveryLine) {

                $orderLine = $deliveryLine->orderLine;

                if (!$orderLine || !$orderLine->product) {
                    throw new \RuntimeException(
                        "Delivery line ID {$deliveryLine->id} has no valid order line or product."
                    );
                }

                $product   = $orderLine->product;
                $variation = $orderLine->productVariation;

                // 🔒 Pricing snapshot from ORDER (authoritative)
                $unitPrice = (float) $orderLine->unit_price_ex_vat;
                $vatRate   = (float) $orderLine->vat_rate;

                InvoiceLineFactory::make(
                    invoiceId: $invoice->id,
                    baseDescription: $product->product_name,
                    quantity: (float) $deliveryLine->processed_quantity,
                    unitPriceExVat: $unitPrice,
                    vatRate: $vatRate,
                    note: null, // editable later by staff
                    source: 'delivery_note',
                    variationLabel: $variation?->display_name
                );
            }

            // ================= RECALCULATE TOTALS =================
            $invoice->load('lines');
            $invoice->refreshTotals();
            $invoice->save();

            // ================= LINK DELIVERY NOTE =================
            $deliveryNote->update([
                'invoice_id' => $invoice->id,
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'Invoice created successfully.');
    }

    /**
     * Generate a digits-only invoice number.
     */
    protected function generateInvoiceNumber(): string
    {
        return now()->format('dmyHis') . random_int(1000, 9999);
    }
}
