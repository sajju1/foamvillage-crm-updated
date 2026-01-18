<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Foam Type Name
        </label>
        <input type="text"
               name="name"
               value="{{ old('name', $foamType->name) }}"
               required
               class="w-full border-gray-300 rounded">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Default Price Unit
        </label>
        <input type="number" step="0.0001"
               name="default_price_unit"
               value="{{ old('default_price_unit', $foamType->default_price_unit) }}"
               required
               class="w-full border-gray-300 rounded">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Default Cost Unit
        </label>
        <input type="number" step="0.0001"
               name="default_cost_unit"
               value="{{ old('default_cost_unit', $foamType->default_cost_unit) }}"
               class="w-full border-gray-300 rounded">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Status
        </label>
        <select name="status" required class="w-full border-gray-300 rounded">
            <option value="active"
                @selected(old('status', $foamType->status) === 'active')>
                Active
            </option>
            <option value="inactive"
                @selected(old('status', $foamType->status) === 'inactive')>
                Inactive
            </option>
        </select>
    </div>

</div>
