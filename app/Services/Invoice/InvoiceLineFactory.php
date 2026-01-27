<?php

namespace App\Services\Invoice;

use App\Models\Invoice\InvoiceLine;

class InvoiceLineFactory
{
    public static function make(
        int $invoiceId,
        string $baseDescription,
        float $quantity,
        float $unitPriceExVat,
        float $vatRate,
        ?string $note = null,
        ?string $source = null,
        ?string $variationLabel = null
    ): InvoiceLine {
        // Build description with variation
        $description = $baseDescription;

        if ($variationLabel) {
            $description .= " ({$variationLabel})";
        }

        $net = round($quantity * $unitPriceExVat, 2);
        $vatAmount = round($net * ($vatRate / 100), 2);
        $gross = $net + $vatAmount;

        return InvoiceLine::create([
            'invoice_id'          => $invoiceId,
            'description'         => $description,
            'note'                => $note,
            'source'              => $source,
            'quantity'            => $quantity,
            'unit_price_ex_vat'   => $unitPriceExVat,
            'vat_rate'            => $vatRate,
            'vat_amount'          => $vatAmount,
            'line_total_inc_vat'  => $gross,
        ]);
    }
}
