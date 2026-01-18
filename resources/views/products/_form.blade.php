<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Company --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Company
        </label>
        <select name="company_id" required class="w-full border-gray-300 rounded">
            <option value="">Select company</option>
            @foreach ($companies as $company)
                <option value="{{ $company->id }}"
                    @selected(old('company_id', $product->company_id) == $company->id)>
                    {{ $company->legal_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Product Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Product Name
        </label>
        <input type="text"
               name="product_name"
               value="{{ old('product_name', $product->product_name) }}"
               required
               class="w-full border-gray-300 rounded">
    </div>

    {{-- Category --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Category
        </label>
        <select name="category_id" required class="w-full border-gray-300 rounded">
            <option value="">Select category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected(old('category_id', $product->category_id) == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Product Type --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Product Type
        </label>
        <select name="product_type" required class="w-full border-gray-300 rounded">
            <option value="simple"
                @selected(old('product_type', $product->product_type) === 'simple')>
                Simple
            </option>
            <option value="variant_based"
                @selected(old('product_type', $product->product_type) === 'variant_based')>
                Variant Based
            </option>
            <option value="rule_based"
                @selected(old('product_type', $product->product_type) === 'rule_based')>
                Formula Based (Foam)
            </option>
        </select>

        <p class="text-xs text-gray-500 mt-1">
            • Simple: direct pricing<br>
            • Variant-based: pricing via variations<br>
            • Formula-based: pricing via foam rules
        </p>
    </div>

    {{-- SIMPLE PRICING (ONLY FOR SIMPLE PRODUCTS) --}}
    <div id="simple-pricing-fields" class="md:col-span-2 hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Sale Price
                </label>
                <input type="number" step="0.01"
                       name="simple_price"
                       value="{{ old('simple_price', $product->simple_price) }}"
                       class="w-full border-gray-300 rounded">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Cost Price
                </label>
                <input type="number" step="0.01"
                       name="simple_cost"
                       value="{{ old('simple_cost', $product->simple_cost) }}"
                       class="w-full border-gray-300 rounded">
            </div>

        </div>
    </div>

    {{-- Manufacturing Type --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Manufacturing Type
        </label>
        <select name="manufacturing_type" required class="w-full border-gray-300 rounded">
            <option value="manufactured"
                @selected(old('manufacturing_type', $product->manufacturing_type) === 'manufactured')>
                Manufactured
            </option>
            <option value="imported"
                @selected(old('manufacturing_type', $product->manufacturing_type) === 'imported')>
                Imported
            </option>
        </select>
    </div>

    {{-- Status --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Status
        </label>
        <select name="status" required class="w-full border-gray-300 rounded">
            <option value="active"
                @selected(old('status', $product->status) === 'active')>
                Active
            </option>
            <option value="inactive"
                @selected(old('status', $product->status) === 'inactive')>
                Inactive
            </option>
        </select>
    </div>

    {{-- Description --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Description
        </label>
        <textarea name="description"
                  rows="4"
                  class="w-full border-gray-300 rounded">{{ old('description', $product->description) }}</textarea>
    </div>

</div>

@push('scripts')
<script>
    function toggleProductTypeFields() {
        const typeSelect = document.querySelector('[name="product_type"]');
        const simplePricing = document.getElementById('simple-pricing-fields');

        if (!typeSelect || !simplePricing) return;

        if (typeSelect.value === 'simple') {
            simplePricing.classList.remove('hidden');
        } else {
            simplePricing.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.querySelector('[name="product_type"]');
        if (!typeSelect) return;

        typeSelect.addEventListener('change', toggleProductTypeFields);
        toggleProductTypeFields(); // initial state
    });
</script>
@endpush
