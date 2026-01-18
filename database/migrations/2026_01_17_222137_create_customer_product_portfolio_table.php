<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_product_portfolio', function (Blueprint $table) {
            $table->id();

            // References (NO foreign keys at this stage)
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variation_id')->nullable();

            // Agreed pricing
            $table->decimal('agreed_price', 10, 2);
            $table->decimal('agreed_cost', 10, 2)->nullable();

            // Validity window
            $table->date('effective_from');
            $table->date('effective_to')->nullable();

            // Status control
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes (safe, no FK dependency)
            $table->index('customer_id');
            $table->index('product_id');
            $table->index('variation_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_product_portfolio');
    }
};
