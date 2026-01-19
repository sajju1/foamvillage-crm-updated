<aside class="w-64 bg-gray-800 text-gray-100 min-h-screen flex flex-col">
    {{-- Logo / Brand --}}
    <div class="px-6 py-4 text-lg font-semibold border-b border-gray-700">
        FoamVillage CRM
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-4 space-y-6 text-sm">

        {{-- MAIN --}}
        <div>
            <p class="px-2 mb-2 text-xs uppercase tracking-wider text-gray-400">
                Main
            </p>

            <a href="{{ route('dashboard') }}"
               class="flex items-center px-3 py-2 rounded-md
               {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                Dashboard
            </a>
        </div>

        {{-- SETUP --}}
        <div>
            <p class="px-2 mb-2 text-xs uppercase tracking-wider text-gray-400">
                Setup
            </p>

            <a href="{{ route('company.index') }}"
               class="flex items-center px-3 py-2 rounded-md
               {{ request()->routeIs('company.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                Companies
            </a>
        </div>

        {{-- PRODUCTS --}}
        <div>
            <p class="px-2 mb-2 text-xs uppercase tracking-wider text-gray-400">
                Products
            </p>

            <a href="{{ route('products.index') }}"
               class="flex items-center px-3 py-2 rounded-md
               {{ request()->routeIs('products.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                Products
            </a>

            <a href="{{ route('product-categories.index') }}"
               class="flex items-center px-3 py-2 rounded-md
               {{ request()->routeIs('product-categories.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                Product Categories
            </a>
        </div>

        {{-- PRICING & FORMULAS --}}
        <div>
            <p class="px-2 mb-2 text-xs uppercase tracking-wider text-gray-400">
                Pricing & Formulas
            </p>

            <a href="{{ route('foam-types.index') }}"
               class="flex items-center px-3 py-2 rounded-md
               {{ request()->routeIs('foam-types.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                Foam Types
            </a>

            <a href="{{ route('pricing.options') }}"
               class="flex items-center px-3 py-2 rounded-md
               {{ request()->routeIs('pricing.options*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                Product Options
            </a>
        </div>

        {{-- CUSTOMERS --}}
        <div>
            <p class="px-2 mb-2 text-xs uppercase tracking-wider text-gray-400">
                Customers
            </p>

            <a href="{{ route('customers.index') }}"
               class="flex items-center px-3 py-2 rounded-md
               {{ request()->routeIs('customers.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                Customers
            </a>
        </div>

        {{-- OPERATIONS --}}
        <div>
            <p class="px-2 mb-2 text-xs uppercase tracking-wider text-gray-400">
                Operations
            </p>

            <div class="px-3 py-2 rounded-md text-gray-500 cursor-not-allowed">
                Orders <span class="ml-2 text-xs">(coming soon)</span>
            </div>

            <div class="px-3 py-2 rounded-md text-gray-500 cursor-not-allowed">
                Invoices <span class="ml-2 text-xs">(coming soon)</span>
            </div>

            <div class="px-3 py-2 rounded-md text-gray-500 cursor-not-allowed">
                Inventory <span class="ml-2 text-xs">(coming soon)</span>
            </div>
        </div>

        {{-- SYSTEM --}}
        <div>
            <p class="px-2 mb-2 text-xs uppercase tracking-wider text-gray-400">
                System
            </p>

            <div class="px-3 py-2 rounded-md text-gray-500 cursor-not-allowed">
                Users & Permissions <span class="ml-2 text-xs">(later)</span>
            </div>
        </div>

    </nav>
</aside>
