@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-gray-800">
            Foam Pricing Rules — {{ $product->product_name }}
        </h1>

        <a href="{{ route('pricing.foam.create', $product) }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            + Add Foam Rule
        </a>
    </div>

    <div class="bg-white shadow rounded overflow-x-auto">
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">
                        Foam Type
                    </th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">
                        Density
                    </th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">
                        Price Unit
                    </th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">
                        Cost Unit
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
                @forelse ($rules as $rule)
                    <tr>
                        {{-- Foam Type (NEW STRUCTURE + FALLBACK) --}}
                        <td class="px-4 py-2">
                            {{ $rule->foamType->name ?? $rule->foam_type ?? '—' }}
                        </td>

                        {{-- Density --}}
                        <td class="px-4 py-2">
                            {{ $rule->density }}
                        </td>

                        {{-- Price Unit --}}
                        <td class="px-4 py-2">
                            {{ rtrim(rtrim(number_format($rule->price_unit, 4), '0'), '.') }}
                        </td>

                        {{-- Cost Unit --}}
                        <td class="px-4 py-2">
                            {{ $rule->cost_unit !== null
                                ? rtrim(rtrim(number_format($rule->cost_unit, 4), '0'), '.')
                                : '—' }}
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs
                                {{ $rule->status === 'active'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-200 text-gray-600' }}">
                                {{ ucfirst($rule->status) }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-2 text-right">
                            <a href="{{ route('pricing.foam.edit', [$product, $rule]) }}"
                               class="text-indigo-600 hover:underline text-sm">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            No foam pricing rules found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <a href="{{ route('products.index') }}"
           class="text-gray-600 hover:underline text-sm">
            ← Back to products
        </a>
    </div>

</div>
@endsection
