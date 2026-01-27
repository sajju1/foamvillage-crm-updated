<?php

namespace App\Http\Controllers\Staff\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice\Invoice;
use App\Models\Product\Product;
use App\Models\Customer\CustomerProductPortfolio;
use App\Services\Invoice\InvoiceLineFactory;
use Illuminate\Http\Request;

class AddInvoiceLineController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        if ($invoice->balance_due <= 0) {
            abort(403, 'This invoice is fully paid and cannot be modified.');
        }
        $invoice->load('customer');

        // ACCOUNT CUSTOMER: must add from portfolio
        if ($invoice->customer->is_account_customer) {

            $data = $request->validate([
                'portfolio_id' => ['required', 'integer', 'exists:customer_product_portfolio,id'],
                'quantity'     => ['required', 'numeric', 'min:0.01'],
                'note'         => ['nullable', 'string'],
            ]);

            // ✅ Pull authoritative price from DB
            $portfolio = \App\Models\Customer\CustomerProductPortfolio::with(['product', 'productVariation'])
                ->where('customer_id', $invoice->customer_id)
                ->where('is_active', true)
                ->findOrFail($data['portfolio_id']);

            $unitPrice = (float) $portfolio->agreed_price;
            $vatRate   = 20.0; // if you later store VAT per product/portfolio, change here

            $desc = $portfolio->product->product_name;

            if ($portfolio->productVariation) {
                $v = $portfolio->productVariation;
                $dims = array_filter([$v->length, $v->width, $v->thickness], fn($x) => $x !== null);
                if (count($dims) >= 2) {
                    $clean = fn($n) => rtrim(rtrim(number_format((float)$n, 2), '0'), '.');
                    $desc .= ' (' . implode(' x ', array_map($clean, $dims)) . ($v->size_unit ? ' ' . $v->size_unit : '') . ')';
                }
            }

            $line = \App\Services\Invoice\InvoiceLineFactory::make(
                invoiceId: $invoice->id,
                baseDescription: $desc,
                quantity: (float) $data['quantity'],
                unitPriceExVat: $unitPrice,
                vatRate: $vatRate
            );

            // Save note if you added note column
            if (!empty($data['note'])) {
                $line->update(['note' => $data['note']]);
            }

            $invoice->refreshTotals();
            $invoice->save();

            return back()->with('success', 'Invoice line added.');
        }

        // WALK-IN / ONE-OFF: can add from catalog OR manual
        $data = $request->validate([
            'variation_id'       => ['nullable', 'integer', 'exists:product_variations,id'],
            'description'        => ['nullable', 'string'],
            'quantity'           => ['required', 'numeric', 'min:0.01'],
            'unit_price_ex_vat'  => ['nullable', 'numeric', 'min:0'],
            'vat_rate'           => ['nullable', 'numeric', 'min:0'],
            'note'               => ['nullable', 'string'],
        ]);

        $vatRate = isset($data['vat_rate']) ? (float) $data['vat_rate'] : 20.0;

        // If variation selected, price comes from DB standard_price
        if (!empty($data['variation_id'])) {

            $variation = \App\Models\Product\ProductVariation::with('product')
                ->findOrFail($data['variation_id']);

            $unitPrice = (float) $variation->standard_price;

            $desc = $variation->product->product_name;

            $dims = array_filter([$variation->length, $variation->width, $variation->thickness], fn($x) => $x !== null);
            if (count($dims) >= 2) {
                $clean = fn($n) => rtrim(rtrim(number_format((float)$n, 2), '0'), '.');
                $desc .= ' (' . implode(' x ', array_map($clean, $dims)) . ($variation->size_unit ? ' ' . $variation->size_unit : '') . ')';
            }

            $line = \App\Services\Invoice\InvoiceLineFactory::make(
                invoiceId: $invoice->id,
                baseDescription: $desc,
                quantity: (float) $data['quantity'],
                unitPriceExVat: $unitPrice,
                vatRate: $vatRate
            );

            if (!empty($data['note'])) {
                $line->update(['note' => $data['note']]);
            }

            $invoice->refreshTotals();
            $invoice->save();

            return back()->with('success', 'Invoice line added.');
        }

        // Manual line: must provide description + unit price
        $data = $request->validate([
            'description'       => ['required', 'string'],
            'quantity'          => ['required', 'numeric', 'min:0.01'],
            'unit_price_ex_vat' => ['required', 'numeric', 'min:0'],
            'vat_rate'          => ['nullable', 'numeric', 'min:0'],
            'note'              => ['nullable', 'string'],
        ]);

        $line = \App\Services\Invoice\InvoiceLineFactory::make(
            invoiceId: $invoice->id,
            baseDescription: $data['description'],
            quantity: (float) $data['quantity'],
            unitPriceExVat: (float) $data['unit_price_ex_vat'],
            vatRate: isset($data['vat_rate']) ? (float) $data['vat_rate'] : 20.0
        );

        if (!empty($data['note'])) {
            $line->update(['note' => $data['note']]);
        }

        $invoice->refreshTotals();
        $invoice->save();

        return back()->with('success', 'Invoice line added.');
    }
    public function destroy(\App\Models\Invoice\InvoiceLine $line)
    {
        $invoice = $line->invoice;

        if ($invoice->balance_due <= 0) {
            abort(403, 'This invoice is fully paid and cannot be modified.');
        }

        $line->delete();

        $invoice->refreshTotals();
        $invoice->save();

        return back()->with('success', 'Invoice line removed.');
    }
}
