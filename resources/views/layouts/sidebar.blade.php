<aside class="w-64 bg-gray-900 text-gray-100 flex flex-col">

    {{-- Logo / Brand --}}
    <div class="px-6 py-4 border-b border-gray-800">
        <span class="text-xl font-semibold tracking-wide">
            Foam Village
        </span>
        <p class="text-xs text-gray-400 mt-1">
            CRM System
        </p>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-6 space-y-6">

        {{-- Section: Setup --}}
        <div>
            <p class="text-xs uppercase tracking-wider text-gray-400 mb-2">
                Setup
            </p>

            <a href="{{ route('company.index') }}"
               class="flex items-center px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800 transition">
                <svg class="h-5 w-5 mr-3 text-gray-400"
                     xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M3 7h18M3 12h18M3 17h18" />
                </svg>
                Companies
            </a>
        </div>

        {{-- Section: Core Modules (disabled for now) --}}
        <div>
            <p class="text-xs uppercase tracking-wider text-gray-400 mb-2">
                Core
            </p>

            <div class="space-y-1 text-gray-500 text-sm">
                <div class="px-3 py-2 rounded-md bg-gray-800 opacity-60">
                    Products
                </div>
                <div class="px-3 py-2 rounded-md bg-gray-800 opacity-60">
                    Customers
                </div>
                <div class="px-3 py-2 rounded-md bg-gray-800 opacity-60">
                    Orders
                </div>
                <div class="px-3 py-2 rounded-md bg-gray-800 opacity-60">
                    Invoices
                </div>
                <div class="px-3 py-2 rounded-md bg-gray-800 opacity-60">
                    Inventory
                </div>
            </div>
        </div>

    </nav>

    {{-- Footer --}}
    <div class="px-6 py-4 border-t border-gray-800 text-xs text-gray-400">
        © {{ date('Y') }} Foam Village
    </div>

</aside>
