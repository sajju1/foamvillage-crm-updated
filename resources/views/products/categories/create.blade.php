@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">

    <h1 class="text-2xl font-semibold text-gray-800 mb-6">
        Create Category
    </h1>

    <div class="bg-white shadow rounded p-6">
        <form method="POST" action="{{ route('product-categories.store') }}">
            @csrf
            @include('products.categories._form')

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('product-categories.index') }}"
                   class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">
                    Cancel
                </a>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Save Category
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
