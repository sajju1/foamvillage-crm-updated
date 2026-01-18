@if (session('success'))
    <div class="mb-4 rounded-md bg-green-50 p-4 border border-green-200">
        <p class="text-sm font-medium text-green-700">
            {{ session('success') }}
        </p>
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200">
        <ul class="text-sm text-red-700 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
