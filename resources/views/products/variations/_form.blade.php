<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    {{-- Dimensions --}}
    <div>
        <label class="block text-sm font-medium mb-1">Length</label>
        <input type="number" step="0.01" name="length"
               value="{{ old('length', $variation->length) }}"
               required
               class="w-full border rounded">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Width</label>
        <input type="number" step="0.01" name="width"
               value="{{ old('width', $variation->width) }}"
               required
               class="w-full border rounded">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Thickness</label>
        <input type="number" step="0.01" name="thickness"
               value="{{ old('thickness', $variation->thickness) }}"
               required
               class="w-full border rounded">
    </div>

    {{-- Unit --}}
    <div>
        <label class="block text-sm font-medium mb-1">Unit</label>
        <select name="size_unit" class="w-full border rounded">
            <option value="inch"
                @selected(old('size_unit', $variation->size_unit) === 'inch')>
                Inch
            </option>
            <option value="cm"
                @selected(old('size_unit', $variation->size_unit) === 'cm')>
                CM
            </option>
        </select>
    </div>

    {{-- Colour --}}
    <div>
        <label class="block text-sm font-medium mb-1">Colour</label>
        <input type="text" name="colour"
               value="{{ old('colour', $variation->colour) }}"
               class="w-full border rounded">
    </div>

    {{-- Variation Code --}}
    <div>
        <label class="block text-sm font-medium mb-1">Variation Code</label>
        <input type="text" name="variation_code"
               value="{{ old('variation_code', $variation->variation_code) }}"
               class="w-full border rounded">
    </div>

    {{-- Pricing --}}
    <div>
        <label class="block text-sm font-medium mb-1">Sale Price</label>
        <input type="number" step="0.01" name="standard_price"
               value="{{ old('standard_price', $variation->standard_price) }}"
               required
               class="w-full border rounded">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Cost Price</label>
        <input type="number" step="0.01" name="standard_cost"
               value="{{ old('standard_cost', $variation->standard_cost) }}"
               class="w-full border rounded">
    </div>

    {{-- Status --}}
    <div>
        <label class="block text-sm font-medium mb-1">Status</label>
        <select name="status" class="w-full border rounded">
            <option value="active"
                @selected(old('status', $variation->status) === 'active')>
                Active
            </option>
            <option value="inactive"
                @selected(old('status', $variation->status) === 'inactive')>
                Inactive
            </option>
        </select>
    </div>

</div>
