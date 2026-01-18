<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Company ownership
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            // Product identity
            $table->string('product_name');
            $table->string('product_category');
            // e.g. mattress, cushion, topper, foam

            $table->enum('product_type', [
                'simple',
                'variant_based',
                'rule_based',
            ])->default('simple');

            $table->enum('manufacturing_type', [
                'manufactured',
                'imported'
            ]);

            $table->text('description')->nullable();

            // Status control (no hard deletes)
            $table->enum('status', ['active', 'inactive'])
                ->default('active');

            $table->timestamps();

            // Indexes
            $table->index('company_id');
            $table->index('product_type');
            $table->index('manufacturing_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
