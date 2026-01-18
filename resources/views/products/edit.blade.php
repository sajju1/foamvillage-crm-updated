@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">

    <h1 class="text-2xl font-semibold text-gray-800 mb-6">
        Edit Product
    </h1>

    <div class="bg-white shadow rounded p-6">
        <form method="POST"
              action="{{ route('products.update', $product) }}">
            @csrf
            @method('PUT')

            @include('products._form')

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('products.index') }}"
                   class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">
                    Cancel
                </a>

                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Update Product
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
