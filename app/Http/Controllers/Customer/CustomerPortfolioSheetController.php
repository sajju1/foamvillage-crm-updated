<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\CustomerPortfolioSheetMail;
use Illuminate\Support\Facades\Mail;

class CustomerPortfolioSheetController extends Controller
{
    /**
     * Print Customer Portfolio Sheet
     * (Canonical data source for print / PDF / email)
     */
    public function print(Customer $customer)
    {
        /*
        |--------------------------------------------------------------------------
        | Issuing Company (authoritative)
        |--------------------------------------------------------------------------
        */
        $company = $customer->company;

        $companyBlock = [
            'name'    => $company->display_name ?? $company->legal_name,
            'phone'   => $company->contact_phone,
            'email'   => $company->contact_email,
            'address' => trim(implode(', ', array_filter([
                $company->address_line1 ?? null,
                $company->address_line2 ?? null,
                $company->city ?? null,
                $company->postcode ?? null,
                $company->country ?? null,
            ]))),
        ];

        /*
        |--------------------------------------------------------------------------
        | Customer (authoritative)
        |--------------------------------------------------------------------------
        */
        $registeredAddress = $customer->registeredAddress;

        $customerBlock = [
            'account_number' => $customer->account_number,
            'company_name'   => $customer->registered_company_name,
            'contact_name'   => $customer->contact_name,
            'phone'          => $customer->primary_phone,
            'address'        => $registeredAddress ? trim(implode(', ', array_filter([
                $registeredAddress->address_line1,
                $registeredAddress->address_line2,
                $registeredAddress->city,
                $registeredAddress->postcode,
                $registeredAddress->country,
            ]))) : null,
        ];

        /*
        |--------------------------------------------------------------------------
        | Portfolio Rows (business intent, never misleading)
        |--------------------------------------------------------------------------
        */
        $portfolioItems = $customer->productPortfolio()
            ->with([
                'product.category',
                'productVariation',
            ])
            ->where('is_active', true)
            ->get();

        $groupedPortfolio = $portfolioItems
            ->groupBy(fn($item) => optional($item->product->category)->name ?? 'Uncategorised')
            ->map(function ($itemsByCategory) {

                return $itemsByCategory
                    ->groupBy(function ($item) {
                        // Keep your safe product-name fallback
                        return $item->product->product_name
                            ?? $item->product->name
                            ?? $item->product->title
                            ?? 'Unnamed Product';
                    })
                    ->map(function ($itemsByProduct) {

                        return $itemsByProduct->map(function ($item) {

                            $isFormula = $item->pricing_type === 'formula';

                            // --- Standard price resolution (FIXED) ---
                            // Variant-based: read from variation price in DB
                            $variationPrice = null;
                            if ($item->productVariation) {
                                // Try common column names safely
                                $variationPrice = $item->productVariation->price
                                    ?? $item->productVariation->standard_price
                                    ?? $item->productVariation->sell_price
                                    ?? null;
                            }

                            // Simple product fallback (if you store it on product)
                            $productPrice = $item->product->price
                                ?? $item->product->standard_price
                                ?? $item->product->simple_price
                                ?? null;

                            $standardPriceDisplay = $isFormula
                                ? 'Formula-based pricing'
                                : ($variationPrice !== null
                                    ? number_format((float)$variationPrice, 2)
                                    : ($productPrice !== null
                                        ? number_format((float)$productPrice, 2)
                                        : '—'));

                            // --- Customer price (your agreed price) ---
                            $customerPriceDisplay = $isFormula
                                ? 'Calculated at order time'
                                : ($item->agreed_price !== null
                                    ? number_format((float)$item->agreed_price, 2)
                                    : '—');

                            return [
                                'label' => $item->productVariation
                                    ? $item->productVariation->display_name
                                    : (
                                        $item->product->product_name
                                        ?? $item->product->name
                                        ?? $item->product->title
                                        ?? 'Unnamed Product'
                                    ),

                                'standard_price' => $standardPriceDisplay,
                                'customer_price' => $customerPriceDisplay,
                            ];
                        })->values();
                    });
            });

        /*
        |--------------------------------------------------------------------------
        | Render
        |--------------------------------------------------------------------------
        */
        return view('customers.portfolio-sheet', [
            'generatedDate'    => now()->format('d M Y'),
            'companyBlock'     => $companyBlock,
            'customerBlock'    => $customerBlock,
            'groupedPortfolio' => $groupedPortfolio,
        ]);
    }

    /**
     * PDF Output
     */
    public function pdf(Customer $customer)
    {
        $viewData = $this->print($customer)->getData();

        return Pdf::loadView('customers.portfolio-sheet', $viewData)
            ->setPaper('a4')
            ->stream('customer-portfolio-' . $customer->account_number . '.pdf');
    }

    public function email(Customer $customer)
    {
        // Reuse the exact same data as print/PDF
        $viewData = $this->print($customer)->getData();

        Mail::to($customer->email)
            ->send(new CustomerPortfolioSheetMail(
                $viewData,
                $customer->account_number
            ));

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Portfolio sheet emailed successfully.');
    }
}
