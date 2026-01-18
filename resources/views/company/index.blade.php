@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">
        Companies
    </h1>

    <a href="{{ route('company.create') }}"
       class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 transition">
        + Add Company
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Company
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Default
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-100">
            @forelse ($companies as $company)
                <tr>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-800">
                            {{ $company->legal_name }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $company->email }}
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        <span class="inline-flex px-2 py-1 text-xs rounded
                            {{ $company->status === 'active'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-gray-200 text-gray-600' }}">
                            {{ ucfirst($company->status) }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        @if ($company->is_default)
                            <span class="inline-flex px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">
                                Default
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">
                                —
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('company.show', $company) }}"
                           class="text-sm text-gray-600 hover:text-gray-900">
                            View
                        </a>

                        <a href="{{ route('company.edit', $company) }}"
                           class="text-sm text-indigo-600 hover:text-indigo-900">
                            Edit
                        </a>

                        <a href="{{ route('brands.index', $company) }}"
                           class="text-sm text-gray-600 hover:text-gray-900">
                            Brands
                        </a>

                        <a href="{{ route('document-defaults.edit', $company) }}"
                           class="text-sm text-gray-600 hover:text-gray-900">
                            Defaults
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-6 text-center text-gray-500">
                        No companies found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection
