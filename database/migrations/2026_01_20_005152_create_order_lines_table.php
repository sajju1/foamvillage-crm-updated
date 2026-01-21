<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_lines', function (Blueprint $table) {
            $table->id();

            // Parent order
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            // Product reference (portfolio-based)
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Optional variation
            $table->foreignId('product_variation_id')
                ->nullable()
                ->constrained('product_variations')
                ->nullOnDelete();

            // Quantities (NO PRICES)
            $table->unsignedInteger('requested_quantity');
            $table->unsignedInteger('processed_quantity')->default(0);

            // Line state (soft cancel only)
            $table->enum('line_status', [
                'active',
                'cancelled',
            ])->default('active');

            // Internal line notes
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('order_id');
            $table->index(['product_id', 'product_variation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_lines');
    }
};
