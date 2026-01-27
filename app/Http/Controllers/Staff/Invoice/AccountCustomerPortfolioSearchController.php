<?php

namespace App\Http\Controllers\Staff\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountCustomerPortfolioSearchController extends Controller
{
    public function __invoke(Request $request, Customer $customer)
    {
        $request->validate([
            'q' => ['required', 'string', 'min:1'],
        ]);

        $term = trim($request->q);

        // Extract numbers: "90 x 50" → [90, 50]
        preg_match_all('/\d+/', $term, $matches);
        $numbers = collect($matches[0])->map(fn ($n) => (float) $n);

        $rows = DB::table('customer_product_portfolio as cpp')
            ->join('products as p', 'p.id', '=', 'cpp.product_id')
            ->leftJoin('product_variations as pv', 'pv.id', '=', 'cpp.product_variation_id')
            ->where('cpp.customer_id', $customer->id)
            ->where('cpp.is_active', true)
            ->where(function ($q) use ($term, $numbers) {

                // ✅ Product name match always allowed
                $q->where('p.product_name', 'like', "%{$term}%");

                // ✅ Dimension search
                if ($numbers->isNotEmpty()) {
                    foreach ($numbers as $num) {
                        $q->orWhere(function ($qq) use ($num) {
                            $qq->where('pv.length', $num)
                               ->orWhere('pv.width', $num)
                               ->orWhere('pv.thickness', $num);
                        });
                    }
                }
            })
            ->select([
                'cpp.id as portfolio_id',
                'p.product_name',
                'pv.length',
                'pv.width',
                'pv.thickness',
                'pv.size_unit',
                'cpp.agreed_price',
            ])
            ->orderBy('p.product_name')
            ->limit(15)
            ->get();

        return response()->json(
            $rows->map(function ($row) {

                $label = $row->product_name;

                // ✅ Clean dimension formatting
                if ($row->length && $row->width) {
                    $dims = [
                        $this->cleanNumber($row->length),
                        $this->cleanNumber($row->width),
                    ];

                    if ($row->thickness) {
                        $dims[] = $this->cleanNumber($row->thickness);
                    }

                    $label .= ' (' . implode(' x ', $dims);

                    if ($row->size_unit) {
                        $label .= ' ' . $row->size_unit;
                    }

                    $label .= ')';
                }

                return [
                    'id'                => $row->portfolio_id, // ✅ CORRECT
                    'label'             => $label,
                    'unit_price_ex_vat' => (float) $row->agreed_price,
                    'vat_rate'          => 20,
                ];
            })
        );
    }

    protected function cleanNumber($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 2), '0'), '.');
    }
}
