@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto py-6">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-semibold">Foam Types</h1>
            <a href="{{ route('foam-types.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded">
                + Add Foam Type
            </a>
        </div>

        <div class="bg-white shadow rounded overflow-x-auto">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Price Unit</th>
                        <th class="px-4 py-2 text-left">Cost Unit</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($foamTypes as $foamType)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $foamType->name }}</td>
                            <td class="px-4 py-2">
                                {{ rtrim(rtrim(number_format($foamType->default_price_unit, 4), '0'), '.') }}
                            </td>

                            <td class="px-4 py-2">
                                {{ $foamType->default_cost_unit !== null
                                    ? rtrim(rtrim(number_format($foamType->default_cost_unit, 4), '0'), '.')
                                    : '—' }}
                            </td>

                            <td class="px-4 py-2 capitalize">{{ $foamType->status }}</td>
                            <td class="px-4 py-2 text-right">
                                <a href="{{ route('foam-types.edit', $foamType) }}"
                                    class="text-indigo-600 hover:underline text-sm">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                No foam types found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection
