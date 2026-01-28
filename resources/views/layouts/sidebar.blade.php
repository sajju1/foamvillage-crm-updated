<aside class="w-64 bg-gray-800 text-gray-100 min-h-screen flex flex-col">
    {{-- Logo / Brand --}}
    <div class="px-6 py-4 text-lg font-semibold border-b border-gray-700">
        FoamVillage CRM
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-4 space-y-6 text-sm">

        {{-- DASHBOARD --}}
        <div>
            <p class="px-2 mb-2 text-xs uppercase tracking-wider text-gray-400">
                Dashboard
            </p>

            <a href="{{ route('dashboard') }}"
               class="flex items-center px-3 py-2 rounded-md
               {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                Overview
            </a>
        </div>

        {{-- COMPANY & SETUP --}}
        <div>
            <p class="px-2 mb-2 text-xs uppercase tracking-wider text-gray-400">
                Company Setup
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
                Categories
            </a>
        </div>

        {{-- PRICING & VAT --}}
        <div>
            <p class="px-2 mb-2 text-xs uppercase tracking-wider text-gray-400">
                Pricing & VAT
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

            <a href="{{ route('staff.vat-rules.index') }}"
               class="flex items-center px-3 py-2 rounded-md
               {{ request()->routeIs('staff.vat-rules.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                VAT Rules
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

            <a href="{{ route('staff.orders.index') }}"
               class="flex items-center px-3 py-2 rounded-md
               {{ request()->routeIs('staff.orders.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                Orders
            </a>

            <a href="{{ route('staff.delivery-notes.index') }}"
               class="flex items-center px-3 py-2 rounded-md
               {{ request()->routeIs('staff.delivery-notes.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                Delivery Notes
            </a>
        </div>

        {{-- FINANCE --}}
        <div>
            <p class="px-2 mb-2 text-xs uppercase tracking-wider text-gray-400">
                Finance
            </p>

            <a href="{{ route('staff.invoices.index') }}"
               class="flex items-center px-3 py-2 rounded-md
               {{ request()->routeIs('staff.invoices.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                Invoices
            </a>

            <a href="{{ route('staff.payments.index') }}"
               class="flex items-center px-3 py-2 rounded-md
               {{ request()->routeIs('staff.payments.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                Payments
            </a>

            {{-- Future-safe placeholders --}}
            <div class="px-3 py-2 rounded-md text-gray-500 cursor-not-allowed">
                Credit Notes <span class="ml-2 text-xs">(next)</span>
            </div>

            <div class="px-3 py-2 rounded-md text-gray-500 cursor-not-allowed">
                Statements <span class="ml-2 text-xs">(later)</span>
            </div>
        </div>

        {{-- SYSTEM --}}
        <div>
            <p class="px-2 mb-2 text-xs uppercase tracking-wider text-gray-400">
                System
            </p>

            <div class="px-3 py-2 rounded-md text-gray-500 cursor-not-allowed">
                Users & Permissions
            </div>
        </div>

    </nav>
</aside>
