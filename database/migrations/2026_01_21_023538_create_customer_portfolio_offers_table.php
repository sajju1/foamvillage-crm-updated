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

            $table->foreignId('customer_product_portfolio_id')
                ->constrained('customer_product_portfolio')
                ->restrictOnDelete();

            $table->string('offer_type');

            $table->decimal('fixed_price', 10, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->integer('minimum_quantity')->nullable(); // reserved for quantity_break (future)

            $table->dateTime('effective_from');
            $table->dateTime('effective_to')->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            // Helpful indexes (safe, non-invasive)
            $table->index(['customer_product_portfolio_id', 'is_active'], 'cpo_portfolio_active_idx');
            $table->index(['effective_from', 'effective_to'], 'cpo_effective_dates_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_portfolio_offers');
    }
};
