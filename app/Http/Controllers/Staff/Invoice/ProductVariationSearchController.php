<?php

namespace App\Http\Controllers\Staff\Invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductVariationSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'q' => ['required', 'string', 'min:1'],
        ]);

        $term = trim($request->q);

        // Extract numbers: "90 x 50" → [90, 50]
        preg_match_all('/\d+/', $term, $matches);
        $numbers = collect($matches[0])->map(fn ($n) => (float) $n);

        $rows = DB::table('products as p')
            ->leftJoin('product_variations as pv', 'pv.product_id', '=', 'p.id')
            ->where(function ($q) use ($term, $numbers) {

                // ✅ Always allow product name match
                $q->where('p.product_name', 'like', "%{$term}%");

                // ✅ Dimension match (safe + optional)
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
                'p.id as product_id',
                'p.product_name',
                'pv.id as variation_id',
                'pv.length',
                'pv.width',
                'pv.thickness',
                'pv.size_unit',
                'pv.standard_price',
            ])
            ->orderBy('p.product_name')
            ->limit(15)
            ->get();

        return response()->json(
            $rows->map(function ($row) {

                $label = $row->product_name;

                // ✅ Clean dimension formatting (NO .00)
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
                    'product_id'        => $row->product_id,
                    'variation_id'      => $row->variation_id,
                    'label'             => $label,
                    'unit_price_ex_vat' => (float) $row->standard_price,
                    'vat_rate'          => 20,
                ];
            })
        );
    }

    /**
     * Remove trailing .00 safely
     */
    protected function cleanNumber($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 2), '0'), '.');
    }
}
