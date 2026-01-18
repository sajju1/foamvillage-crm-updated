<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_options', function (Blueprint $table) {
            $table->id();

            // Option identity
            $table->string('option_name');
            /*
             * Examples:
             * foam_only
             * cover_only
             * complete_product
             * compression
             * drop_shipping
             */

            // How this option modifies price
            $table->enum('price_modifier_type', [
                'fixed_add',
                'fixed_deduct',
                'percentage'
            ]);

            $table->decimal('price_modifier_value', 10, 2);

            // Optional cost modifier (manufacturing / logistics)
            $table->decimal('cost_modifier_value', 10, 2)->nullable();

            // Status control
            $table->enum('status', ['active', 'inactive'])
                ->default('active');

            $table->timestamps();

            // Indexes
            $table->index('option_name');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_options');
    }
};
