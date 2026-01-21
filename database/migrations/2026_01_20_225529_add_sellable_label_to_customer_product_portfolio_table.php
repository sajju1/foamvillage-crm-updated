<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_product_portfolio', function (Blueprint $table) {
            $table->string('sellable_label')->after('product_variation_id');
        });
    }

    public function down(): void
    {
        Schema::table('customer_product_portfolio', function (Blueprint $table) {
            $table->dropColumn('sellable_label');
        });
    }
};
