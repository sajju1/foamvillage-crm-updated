@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            Products
        </h1>

        <a href="{{ route('products.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            + Add Product
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white shadow rounded p-4 mb-6">
        <form method="GET" action="{{ route('products.index') }}"
              class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Search
                </label>
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Search by product name..."
                       class="w-full border-gray-300 rounded">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Category
                </label>
                <select name="category_id" class="w-full border-gray-300 rounded">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            @selected((string)request('category_id') === (string)$category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Apply
                </button>

                <a href="{{ route('products.index') }}"
                   class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">
                    Reset
                </a>
            </div>

        </form>
    </div>

    <div class="bg-white shadow rounded overflow-x-auto">
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">
                        Product Name
                    </th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">
                        Category
                    </th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">
                        Type
                    </th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">
                        Company
                    </th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">
                        Status
                    </th>
                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-700">
                        Actions
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-4 py-2">
                            {{ $product->product_name }}
                        </td>

                        <td class="px-4 py-2">
                            {{ $product->category->name ?? '—' }}
                        </td>

                        <td class="px-4 py-2 capitalize">
                            {{ str_replace('_', ' ', $product->product_type) }}
                        </td>

                        <td class="px-4 py-2">
                            {{ $product->company->legal_name ?? '—' }}
                        </td>

                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs
                                {{ $product->status === 'active'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-200 text-gray-600' }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </td>

                        <td class="px-4 py-2 text-right space-x-2">
                            <a href="{{ route('products.edit', $product) }}"
                               class="text-indigo-600 hover:underline text-sm">
                                Edit
                            </a>

                            @if ($product->product_type === 'variant_based')
                                <a href="{{ route('products.variations.index', $product) }}"
                                   class="text-gray-600 hover:underline text-sm">
                                    Variations
                                </a>
                            @endif

                            @if ($product->product_type === 'rule_based')
                                <a href="{{ route('pricing.foam.index', $product) }}"
                                   class="text-gray-600 hover:underline text-sm">
                                    Foam Rules
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            No products found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $products->links() }}
    </div>

</div>
@endsection
