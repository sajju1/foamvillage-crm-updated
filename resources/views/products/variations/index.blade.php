@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">
                Variations – {{ $product->product_name }}
            </h1>
            <p class="text-sm text-gray-500">
                Variant-based product
            </p>
        </div>

        <a href="{{ route('products.variations.create', $product) }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            + Add Variation
        </a>
    </div>

    <div class="bg-white shadow rounded overflow-x-auto">
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Code</th>
                    <th class="px-4 py-2 text-left">Size</th>
                    <th class="px-4 py-2 text-left">Colour</th>
                    <th class="px-4 py-2 text-left">Price</th>
                    <th class="px-4 py-2 text-left">Cost</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse ($variations as $variation)
                    <tr>
                        <td class="px-4 py-2">
                            {{ $variation->variation_code ?? '—' }}
                        </td>

                        <td class="px-4 py-2">
                            {{ $variation->length }} × {{ $variation->width }} × {{ $variation->thickness }}
                            {{ $variation->size_unit }}
                        </td>

                        <td class="px-4 py-2">
                            {{ $variation->colour ?? '—' }}
                        </td>

                        <td class="px-4 py-2">
                            {{ number_format($variation->standard_price, 2) }}
                        </td>

                        <td class="px-4 py-2">
                            {{ $variation->standard_cost !== null
                                ? number_format($variation->standard_cost, 2)
                                : '—' }}
                        </td>

                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded
                                {{ $variation->status === 'active'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-200 text-gray-600' }}">
                                {{ ucfirst($variation->status) }}
                            </span>
                        </td>

                        <td class="px-4 py-2 text-right">
                            <a href="{{ route('products.variations.edit', [$product, $variation]) }}"
                               class="text-indigo-600 hover:underline text-sm">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                            No variations added yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <a href="{{ route('products.index') }}"
           class="text-gray-600 hover:underline text-sm">
            ← Back to Products
        </a>
    </div>

</div>
@endsection
