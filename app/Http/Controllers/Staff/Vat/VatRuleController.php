<?php

namespace App\Http\Controllers\Staff\Vat;

use App\Http\Controllers\Controller;
use App\Models\Vat\VatRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VatRuleController extends Controller
{
    /**
     * Display all VAT rules
     */
    public function index()
    {
        $vatRules = VatRule::orderByDesc('is_default')
            ->orderBy('rate')
            ->get();

        return view('staff.vat-rules.index', compact('vatRules'));
    }

    /**
     * Store a new VAT rule
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255', 'unique:vat_rules,name'],
            'rate'       => ['required', 'numeric', 'min:0'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($validated) {

            // If new rule is default → unset existing default
            if (!empty($validated['is_default'])) {
                VatRule::where('is_default', true)
                    ->update(['is_default' => false]);
            }

            VatRule::create([
                'name'       => $validated['name'],
                'rate'       => $validated['rate'],
                'is_default' => $validated['is_default'] ?? false,
                'is_active'  => true,
            ]);
        });

        return redirect()
            ->route('staff.vat-rules.index')
            ->with('success', 'VAT rule created successfully.');
    }

    /**
     * Deactivate a VAT rule (never delete)
     */
    public function deactivate(VatRule $vatRule)
    {
        // Never allow default VAT to be deactivated
        if ($vatRule->is_default) {
            return back()->with('error', 'Default VAT rule cannot be deactivated.');
        }

        $vatRule->update([
            'is_active' => false,
        ]);

        return back()->with('success', 'VAT rule deactivated.');
    }
}
