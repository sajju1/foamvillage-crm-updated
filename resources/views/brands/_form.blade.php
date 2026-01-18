@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Brand Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Brand Name
        </label>
        <input type="text"
               name="brand_name"
               value="{{ old('brand_name', $brand->brand_name ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm"
               required>
    </div>

    {{-- Brand Email --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Brand Email
        </label>
        <input type="email"
               name="brand_email"
               value="{{ old('brand_email', $brand->brand_email ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm">
    </div>

    {{-- Brand Phone --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Brand Phone
        </label>
        <input type="text"
               name="brand_phone"
               value="{{ old('brand_phone', $brand->brand_phone ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm">
    </div>

    {{-- Brand Website --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Brand Website
        </label>
        <input type="text"
               name="brand_website"
               value="{{ old('brand_website', $brand->brand_website ?? '') }}"
               class="w-full border-gray-300 rounded-md shadow-sm">
    </div>

    {{-- Default Brand --}}
    <div class="flex items-center space-x-2 mt-2">
        <input type="checkbox"
               name="is_default_brand"
               value="1"
               {{ old('is_default_brand', $brand->is_default_brand ?? false) ? 'checked' : '' }}>
        <span class="text-sm text-gray-700">
            Set as default brand
        </span>
    </div>

    {{-- Status --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Status
        </label>
        <select name="status"
                class="w-full border-gray-300 rounded-md shadow-sm">
            <option value="active"
                {{ old('status', $brand->status ?? 'active') === 'active' ? 'selected' : '' }}>
                Active
            </option>
            <option value="inactive"
                {{ old('status', $brand->status ?? '') === 'inactive' ? 'selected' : '' }}>
                Inactive
            </option>
        </select>
    </div>

</div>

<div class="mt-6 flex justify-end">
    <button type="submit"
            class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
        Save Brand
    </button>
</div>
