@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- ===================== --}}
    {{-- Legal Information --}}
    {{-- ===================== --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Legal Name</label>
        <input type="text"
               name="legal_name"
               value="{{ old('legal_name', $company->legal_name ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Company Number</label>
        <input type="text"
               name="company_number"
               value="{{ old('company_number', $company->company_number ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">VAT Number</label>
        <input type="text"
               name="vat_number"
               value="{{ old('vat_number', $company->vat_number ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm">
    </div>

    {{-- ===================== --}}
    {{-- Address --}}
    {{-- ===================== --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
        <input type="text"
               name="address_line1"
               value="{{ old('address_line1', $company->address_line1 ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
        <input type="text"
               name="address_line2"
               value="{{ old('address_line2', $company->address_line2 ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 3</label>
        <input type="text"
               name="address_line3"
               value="{{ old('address_line3', $company->address_line3 ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
        <input type="text"
               name="city"
               value="{{ old('city', $company->city ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">County / Region</label>
        <input type="text"
               name="state_region"
               value="{{ old('state_region', $company->state_region ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
        <input type="text"
               name="country"
               value="{{ old('country', $company->country ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Postcode</label>
        <input type="text"
               name="postcode"
               value="{{ old('postcode', $company->postcode ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm"
               required>
    </div>

    {{-- ===================== --}}
    {{-- Contact --}}
    {{-- ===================== --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email"
               name="email"
               value="{{ old('email', $company->email ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
        <input type="text"
               name="phone"
               value="{{ old('phone', $company->phone ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm">
    </div>

    {{-- ===================== --}}
    {{-- Banking --}}
    {{-- ===================== --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
        <input type="text"
               name="bank_name"
               value="{{ old('bank_name', $company->bank_name ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
        <input type="text"
               name="bank_account_name"
               value="{{ old('bank_account_name', $company->bank_account_name ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
        <input type="text"
               name="bank_account_number"
               value="{{ old('bank_account_number', $company->bank_account_number ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Sort Code</label>
        <input type="text"
               name="bank_sort_code"
               value="{{ old('bank_sort_code', $company->bank_sort_code ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm"
               placeholder="00-00-00">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">IBAN</label>
        <input type="text"
               name="bank_iban"
               value="{{ old('bank_iban', $company->bank_iban ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">SWIFT / BIC</label>
        <input type="text"
               name="bank_swift_bic"
               value="{{ old('bank_swift_bic', $company->bank_swift_bic ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm">
    </div>

    {{-- ===================== --}}
    {{-- System Flags --}}
    {{-- ===================== --}}
    <div class="flex items-center space-x-2 mt-2">
        <input type="checkbox"
               name="is_default"
               value="1"
               {{ old('is_default', $company->is_default ?? false) ? 'checked' : '' }}>
        <span class="text-sm text-gray-700">
            Set as default company
        </span>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select name="status" class="w-full border-gray-300 rounded-md shadow-sm">
            <option value="active"
                {{ old('status', $company->status ?? 'active') === 'active' ? 'selected' : '' }}>
                Active
            </option>
            <option value="inactive"
                {{ old('status', $company->status ?? '') === 'inactive' ? 'selected' : '' }}>
                Inactive
            </option>
        </select>
    </div>

</div>

<div class="mt-6 flex justify-end">
    <button type="submit"
            class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
        Save Company
    </button>
</div>
