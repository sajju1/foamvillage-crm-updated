@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">

    <h1 class="text-2xl font-semibold text-gray-800 mb-6">
        {{ $category->exists ? 'Edit Category' : 'Create Category' }}
    </h1>

    <div class="bg-white shadow rounded p-6">
        <form method="POST"
              action="{{ $category->exists
                    ? route('product-categories.update', $category)
                    : route('product-categories.store') }}">

            @csrf
            @if ($category->exists)
                @method('PUT')
            @endif

            <div class="space-y-4">

                {{-- Company --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Company
                    </label>
                    <select name="company_id" class="w-full border-gray-300 rounded">
                        <option value="">Global</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}"
                                @selected(old('company_id', $category->company_id) == $company->id)>
                                {{ $company->legal_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Category Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Category Name
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $category->name) }}"
                           required
                           class="w-full border-gray-300 rounded">
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Status
                    </label>
                    <select name="status" class="w-full border-gray-300 rounded">
                        <option value="active"
                            @selected(old('status', $category->status) === 'active')>
                            Active
                        </option>
                        <option value="inactive"
                            @selected(old('status', $category->status) === 'inactive')>
                            Inactive
                        </option>
                    </select>
                </div>

            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('product-categories.index') }}"
                   class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">
                    Cancel
                </a>

                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Save Category
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
