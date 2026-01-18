<nav class="bg-white border-b border-gray-200">
    <div class="flex items-center justify-between px-6 py-3">

        {{-- Left: Page title / breadcrumb placeholder --}}
        <div class="flex items-center space-x-3">
            <h1 class="text-lg font-semibold text-gray-800">
                {{ config('app.name', 'Foam Village CRM') }}
            </h1>
        </div>

        {{-- Right: User menu --}}
        <div class="flex items-center space-x-4">

            {{-- Authenticated user --}}
            <div class="relative">
                <details class="group">
                    <summary class="flex items-center cursor-pointer list-none">
                        <span class="text-sm font-medium text-gray-700">
                            {{ Auth::user()->name ?? 'User' }}
                        </span>
                        <svg class="ml-2 h-4 w-4 text-gray-500 group-open:rotate-180 transition"
                             xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>

                    <div class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded shadow">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Log out
                            </button>
                        </form>
                    </div>
                </details>
            </div>

        </div>
    </div>
</nav>
