@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">
                Create Brand
            </h1>
            <p class="text-sm text-gray-500">
                Company: {{ $company->legal_name }}
            </p>
        </div>

        <a href="{{ route('brands.index', $company) }}"
           class="text-sm text-gray-600 hover:text-gray-900">
            ← Back to Brands
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('brands.store', $company) }}">
            @include('brands._form')
        </form>
    </div>

</div>

@endsection
