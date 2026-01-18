@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <h1 class="text-xl font-semibold mb-6">Create Foam Type</h1>

    <form method="POST" action="{{ route('foam-types.store') }}">
        @csrf
        @include('products.foam-types._form')

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('foam-types.index') }}" class="px-4 py-2 border rounded">
                Cancel
            </a>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded">
                Save
            </button>
        </div>
    </form>
</div>
@endsection
