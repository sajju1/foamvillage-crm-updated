<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foam_pricing_rules', function (Blueprint $table) {
            $table->id();

            // Parent product (must be rule-based / foam)
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Foam characteristics
            $table->string('foam_type');
            $table->string('density');

            // Pricing units
            $table->decimal('price_unit', 10, 2);
            $table->decimal('cost_unit', 10, 2)->nullable();

            // Formula reference (stored for clarity / future flexibility)
            // Example: (L × W × T) / 144 × price_unit
            $table->string('calculation_formula');

            // Status control
            $table->enum('status', ['active', 'inactive'])
                ->default('active');

            $table->timestamps();

            // Indexes
            $table->index('product_id');
            $table->index('foam_type');
            $table->index('density');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foam_pricing_rules');
    }
};
