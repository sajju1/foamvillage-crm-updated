@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            Create Company
        </h1>

        <a href="{{ route('company.index') }}"
           class="text-sm text-gray-600 hover:text-gray-900">
            ← Back to Companies
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('company.store') }}">
            @include('company._form')
        </form>
    </div>

</div>

@endsection
