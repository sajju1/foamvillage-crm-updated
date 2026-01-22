<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Safety: only proceed if table exists
        if (!Schema::hasTable('customer_portfolio_offers')) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Drop legacy foreign keys & columns (if they exist)
        |--------------------------------------------------------------------------
        */
        Schema::table('customer_portfolio_offers', function (Blueprint $table) {

            // customer_id
            if (Schema::hasColumn('customer_portfolio_offers', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }

            // product_id
            if (Schema::hasColumn('customer_portfolio_offers', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }

            // product_variation_id
            if (Schema::hasColumn('customer_portfolio_offers', 'product_variation_id')) {
                $table->dropForeign(['product_variation_id']);
                $table->dropColumn('product_variation_id');
            }

            // legacy pricing / dates
            if (Schema::hasColumn('customer_portfolio_offers', 'offer_value')) {
                $table->dropColumn('offer_value');
            }

            if (Schema::hasColumn('customer_portfolio_offers', 'valid_from')) {
                $table->dropColumn('valid_from');
            }

            if (Schema::hasColumn('customer_portfolio_offers', 'valid_to')) {
                $table->dropColumn('valid_to');
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Add canonical portfolio-based columns
        |--------------------------------------------------------------------------
        */
        Schema::table('customer_portfolio_offers', function (Blueprint $table) {

            if (!Schema::hasColumn('customer_portfolio_offers', 'customer_product_portfolio_id')) {
                $table->foreignId('customer_product_portfolio_id')
                    ->after('id')
                    ->constrained('customer_product_portfolio')
                    ->restrictOnDelete();
            }

            if (!Schema::hasColumn('customer_portfolio_offers', 'offer_type')) {
                $table->string('offer_type')->after('customer_product_portfolio_id');
            }

            if (!Schema::hasColumn('customer_portfolio_offers', 'fixed_price')) {
                $table->decimal('fixed_price', 10, 2)->nullable();
            }

            if (!Schema::hasColumn('customer_portfolio_offers', 'percentage')) {
                $table->decimal('percentage', 5, 2)->nullable();
            }

            if (!Schema::hasColumn('customer_portfolio_offers', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->nullable();
            }

            if (!Schema::hasColumn('customer_portfolio_offers', 'minimum_quantity')) {
                $table->integer('minimum_quantity')->nullable();
            }

            if (!Schema::hasColumn('customer_portfolio_offers', 'effective_from')) {
                $table->dateTime('effective_from');
            }

            if (!Schema::hasColumn('customer_portfolio_offers', 'effective_to')) {
                $table->dateTime('effective_to')->nullable();
            }

            if (!Schema::hasColumn('customer_portfolio_offers', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }

            if (!Schema::hasColumn('customer_portfolio_offers', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Intentionally left empty.
        // This migration rebuilds legacy schema safely and should not auto-rollback.
    }
};
