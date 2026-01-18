@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">
                Brands
            </h1>
            <p class="text-sm text-gray-500">
                Company: {{ $company->legal_name }}
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('company.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Companies
            </a>

            <a href="{{ route('brands.create', $company) }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                + Add Brand
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Brand Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Default
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                        Actions
                    </th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-100">
                @forelse ($brands as $brand)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ $brand->brand_name }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs rounded
                                {{ $brand->status === 'active'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-200 text-gray-600' }}">
                                {{ ucfirst($brand->status) }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            @if ($brand->is_default_brand)
                                <span class="inline-flex px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">
                                    Default
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('brands.edit', [$company, $brand]) }}"
                               class="text-sm text-indigo-600 hover:text-indigo-900">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-gray-500">
                            No brands found.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</div>

@endsection
