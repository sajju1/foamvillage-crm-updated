<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();

            // Parent product
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Physical dimensions
            $table->decimal('length', 8, 2);
            $table->decimal('width', 8, 2);
            $table->decimal('thickness', 8, 2);

            $table->enum('size_unit', ['inch', 'cm'])
                ->default('inch');

            // Optional attributes
            $table->string('colour')->nullable();

            // Internal reference / SKU-like code
            $table->string('variation_code')->nullable();

            // Standard pricing (base reference price)
            $table->decimal('standard_price', 10, 2);
            $table->decimal('standard_cost', 10, 2)->nullable();

            // Status control
            $table->enum('status', ['active', 'inactive'])
                ->default('active');

            $table->timestamps();

            // Indexes
            $table->index('product_id');
            $table->index('status');
            $table->index('variation_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
