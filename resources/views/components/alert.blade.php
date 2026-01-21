@if (session('success'))
    <div class="mb-4 rounded border border-green-200 bg-green-50 p-4 text-green-800">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 rounded border border-red-200 bg-red-50 p-4 text-red-800">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded border border-red-200 bg-red-50 p-4 text-red-800">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
