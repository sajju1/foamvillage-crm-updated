@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">

    <div class="flex justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            Product Categories
        </h1>

        <a href="{{ route('product-categories.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            Add Category
        </a>
    </div>

    <div class="bg-white shadow rounded">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Company</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr class="border-t">
                        <td class="px-4 py-2">
                            {{ $category->name }}
                        </td>
                        <td class="px-4 py-2">
                            {{ $category->company->legal_name ?? 'Global' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ ucfirst($category->status) }}
                        </td>
                        <td class="px-4 py-2 text-right">
                            <a href="{{ route('product-categories.edit', $category) }}"
                               class="text-indigo-600 hover:underline text-sm">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                            No categories found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
