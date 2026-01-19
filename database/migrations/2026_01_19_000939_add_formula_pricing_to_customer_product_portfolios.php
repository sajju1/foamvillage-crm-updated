<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customer_product_portfolio', function (Blueprint $table) {

            $table->enum('pricing_type', ['fixed', 'formula'])
                ->default('fixed')
                ->after('product_variation_id');

            $table->enum('formula_pricing_mode', [
                'standard',
                'rate_override',
                'percentage_modifier',
            ])->nullable()
                ->after('agreed_price');

            $table->decimal('rate_override', 10, 2)
                ->nullable()
                ->after('formula_pricing_mode');

            $table->decimal('percentage_modifier', 5, 2)
                ->nullable()
                ->after('rate_override');

            $table->decimal('minimum_charge', 10, 2)
                ->nullable()
                ->after('percentage_modifier');

            $table->enum('rounding_rule', [
                'none',
                'nearest_0.5',
                'nearest_1',
            ])->nullable()
                ->after('minimum_charge');
        });
    }

    public function down(): void
    {
        Schema::table('customer_product_portfolio', function (Blueprint $table) {
            $table->dropColumn([
                'pricing_type',
                'formula_pricing_mode',
                'rate_override',
                'percentage_modifier',
                'minimum_charge',
                'rounding_rule',
            ]);
        });
    }
};
