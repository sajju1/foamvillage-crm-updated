<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Foam Type --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Foam Type
        </label>
        <select name="foam_type_id"
                required
                class="w-full border-gray-300 rounded">
            <option value="">Select foam type</option>
            @foreach ($foamTypes as $foamType)
                <option value="{{ $foamType->id }}"
                    @selected(old('foam_type_id', $rule->foam_type_id) == $foamType->id)>
                    {{ $foamType->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Density --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Density
        </label>
        <input type="number"
               step="0.01"
               name="density"
               value="{{ old('density', $rule->density) }}"
               required
               class="w-full border-gray-300 rounded">
    </div>

    {{-- Price Unit --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Price Unit (per cubic ft)
        </label>
        <input type="number"
               step="0.0001"
               name="price_unit"
               value="{{ old('price_unit', $rule->price_unit) }}"
               placeholder="Leave blank to use default"
               class="w-full border-gray-300 rounded">
    </div>

    {{-- Cost Unit --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Cost Unit (per cubic ft)
        </label>
        <input type="number"
               step="0.0001"
               name="cost_unit"
               value="{{ old('cost_unit', $rule->cost_unit) }}"
               placeholder="Leave blank to use default"
               class="w-full border-gray-300 rounded">
    </div>

    {{-- Calculation Formula --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Calculation Formula (optional)
        </label>
        <input type="text"
               name="calculation_formula"
               value="{{ old('calculation_formula', $rule->calculation_formula) }}"
               placeholder="e.g. (L × W × T) / 144 × price_unit"
               class="w-full border-gray-300 rounded">
        <p class="text-xs text-gray-500 mt-1">
            Informational only — price is calculated at order time.
        </p>
    </div>

    {{-- Status --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Status
        </label>
        <select name="status"
                required
                class="w-full border-gray-300 rounded">
            <option value="active"
                @selected(old('status', $rule->status ?? 'active') === 'active')>
                Active
            </option>
            <option value="inactive"
                @selected(old('status', $rule->status) === 'inactive')>
                Inactive
            </option>
        </select>
    </div>

</div>
