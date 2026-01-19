@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 space-y-4">
    <div>
        <h1 class="text-xl font-semibold">Create Customer</h1>
        <p class="text-sm text-gray-600">
            Customer will be created under the selected company.
        </p>
    </div>

    <form method="POST" action="{{ route('customers.store') }}">
        @csrf

        @include('customers._form', [
            'customer' => null,
            'companies' => $companies,
            'defaultCompanyId' => $defaultCompanyId
        ])

        <div class="mt-6">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Save Customer
            </button>
        </div>
    </form>
</div>
@endsection
