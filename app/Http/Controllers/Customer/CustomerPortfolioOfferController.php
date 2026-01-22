<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\CustomerPortfolioOffer;
use App\Models\Customer\CustomerProductPortfolio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;



class CustomerPortfolioOfferController extends Controller
{
    /**
     * Store a new offer for a portfolio entry.
     */
    public function store(Request $request, CustomerProductPortfolio $portfolioEntry)
    {
        $validated = $request->validate([
            'offer_type'      => ['required', 'string'],
            'fixed_price'     => ['nullable', 'numeric', 'min:0'],
            'percentage'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'effective_from'  => ['required', 'date'],
            'effective_to'    => ['nullable', 'date', 'after_or_equal:effective_from'],
            'notes'           => ['nullable', 'string'],
        ]);

        CustomerPortfolioOffer::create([
            'customer_product_portfolio_id' => $portfolioEntry->id,
            'offer_type'                     => $validated['offer_type'],
            'fixed_price'                    => $validated['fixed_price'] ?? null,
            'percentage'                     => $validated['percentage'] ?? null,
            'discount_amount'                => $validated['discount_amount'] ?? null,
            'effective_from'                 => Carbon::parse($validated['effective_from']),
            'effective_to'                   => isset($validated['effective_to'])
                ? Carbon::parse($validated['effective_to'])
                : null,
            'is_active'                      => true,
            'created_by' => Auth::id(),
            'notes'                          => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Offer created successfully.');
    }

    /**
     * Deactivate (cancel) an offer.
     */
    public function deactivate(CustomerPortfolioOffer $offer)
    {
        $offer->update([
            'is_active' => false,
        ]);

        return back()->with('success', 'Offer deactivated.');
    }
}
