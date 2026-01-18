<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\FoamType;
use Illuminate\Http\Request;

class FoamTypeController extends Controller
{
    public function index()
    {
        $foamTypes = FoamType::orderBy('name')->get();
        return view('products.foam-types.index', compact('foamTypes'));
    }

    public function create()
    {
        return view('products.foam-types.create', [
            'foamType' => new FoamType(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:foam_types,name',
            'default_price_unit' => 'required|numeric|min:0',
            'default_cost_unit' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        FoamType::create($validated);

        return redirect()
            ->route('foam-types.index')
            ->with('success', 'Foam type created successfully.');
    }

    public function edit(FoamType $foamType)
    {
        return view('products.foam-types.edit', compact('foamType'));
    }

    public function update(Request $request, FoamType $foamType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:foam_types,name,' . $foamType->id,
            'default_price_unit' => 'required|numeric|min:0',
            'default_cost_unit' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $foamType->update($validated);

        return redirect()
            ->route('foam-types.index')
            ->with('success', 'Foam type updated successfully.');
    }
}
