<div class="bg-white p-6 rounded shadow space-y-6">

    {{-- Company (customer belongs to one company) --}}
    <div>
        <label class="block text-sm font-medium mb-1">
            Company *
        </label>
        <select name="company_id" class="border rounded px-3 py-2 w-full">
            @foreach ($companies as $company)
                <option value="{{ $company->id }}" @selected(old('company_id', $customer->company_id ?? $defaultCompanyId) == $company->id)>
                    {{ $company->legal_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Contact details --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">
                Contact name *
            </label>
            <input type="text" name="contact_name" class="border rounded px-3 py-2 w-full"
                value="{{ old('contact_name', $customer->contact_name ?? '') }}">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">
                Email *
            </label>
            <input type="email" name="email" class="border rounded px-3 py-2 w-full"
                value="{{ old('email', $customer->email ?? '') }}">
        </div>
    </div>

    {{-- Phone numbers --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">
                Primary phone *
            </label>
            <input type="text" name="primary_phone" class="border rounded px-3 py-2 w-full"
                value="{{ old('primary_phone', $customer->primary_phone ?? '') }}">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">
                Secondary phone
            </label>
            <input type="text" name="secondary_phone" class="border rounded px-3 py-2 w-full"
                value="{{ old('secondary_phone', $customer->secondary_phone ?? '') }}">
        </div>
    </div>

    {{-- Customer business identity (belongs to CUSTOMER, not COMPANY) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">
                Registered company name
            </label>
            <input type="text" name="registered_company_name" class="border rounded px-3 py-2 w-full"
                value="{{ old('registered_company_name', $customer->registered_company_name ?? '') }}">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">
                Customer registration number
            </label>
            <input type="text" name="customer_registration_number" class="border rounded px-3 py-2 w-full"
                value="{{ old('customer_registration_number', $customer->customer_registration_number ?? '') }}">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">
                VAT number
            </label>
            <input type="text" name="vat_number" class="border rounded px-3 py-2 w-full"
                value="{{ old('vat_number', $customer->vat_number ?? '') }}">
        </div>
    </div>

    {{-- Status & financial controls --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">
                Customer status *
            </label>
            @php
                $status = old('customer_status', $customer->customer_status ?? 'active');
            @endphp
            <select name="customer_status" class="border rounded px-3 py-2 w-full">
                <option value="active" @selected($status === 'active')>Active</option>
                <option value="on_hold" @selected($status === 'on_hold')>On hold</option>
                <option value="blocked" @selected($status === 'blocked')>Blocked</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">
                Credit limit
            </label>
            <input type="number" step="0.01" name="credit_limit" class="border rounded px-3 py-2 w-full"
                value="{{ old('credit_limit', $customer->credit_limit ?? '') }}">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">
                Payment terms *
            </label>
            @php
                $terms = old('payment_terms', $customer->payment_terms ?? 'immediate');
            @endphp
            <select name="payment_terms" class="border rounded px-3 py-2 w-full">
                <option value="immediate" @selected($terms === 'immediate')>Immediate</option>
                <option value="7_days" @selected($terms === '7_days')>7 days</option>
                <option value="14_days" @selected($terms === '14_days')>14 days</option>
                <option value="30_days" @selected($terms === '30_days')>30 days</option>
                <option value="custom" @selected($terms === 'custom')>Custom</option>
            </select>
        </div>
    </div>

    {{-- Internal notes --}}
    <div>
        <label class="block text-sm font-medium mb-1">
            Internal notes
        </label>
        <textarea name="internal_notes" rows="3" class="border rounded px-3 py-2 w-full">{{ old('internal_notes', $customer->internal_notes ?? '') }}</textarea>
    </div>

</div>
