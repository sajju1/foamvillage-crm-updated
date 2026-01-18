@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">

    <h1 class="text-2xl font-semibold mb-6">
        Add Foam Pricing Rule – {{ $product->product_name }}
    </h1>

    <div class="bg-white shadow rounded p-6">
        <form method="POST"
              action="{{ route('pricing.foam.store', $product) }}">
            @csrf

            @include('products.pricing.foam._form')

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('pricing.foam.index', $product) }}"
                   class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">
                    Cancel
                </a>

                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Save Rule
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
