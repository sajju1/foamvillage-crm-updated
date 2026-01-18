<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_discounts', function (Blueprint $table) {
            $table->id();

            // References (NO foreign keys at this stage)
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('variation_id')->nullable();

            // Discount definition
            $table->enum('discount_type', [
                'fixed',
                'percentage',
            ]);

            $table->decimal('discount_value', 10, 2);

            // Validity window
            $table->date('valid_from');
            $table->date('valid_to');

            $table->timestamps();

            // Indexes (safe, no FK dependency)
            $table->index('customer_id');
            $table->index('product_id');
            $table->index('variation_id');
            $table->index(['valid_from', 'valid_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_discounts');
    }
};
