<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_portfolio_offers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->foreignId('product_variation_id')
                ->nullable()
                ->constrained('product_variations')
                ->nullOnDelete();

            $table->enum('offer_type', [
                'fixed_price',
                'percentage_discount'
            ]);

            $table->decimal('offer_value', 12, 4);

            $table->date('valid_from');
            $table->date('valid_to');

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            // Indexes for pricing resolution
            $table->index(['customer_id', 'valid_from', 'valid_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_portfolio_offers');
    }
};
