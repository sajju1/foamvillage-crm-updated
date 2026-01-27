<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('order_lines', function (Blueprint $table) {
        $table->decimal('unit_price_ex_vat', 10, 2)->default(0)->after('requested_quantity');
        $table->decimal('vat_rate', 5, 2)->default(0)->after('unit_price_ex_vat');
    });
}

public function down(): void
{
    Schema::table('order_lines', function (Blueprint $table) {
        $table->dropColumn(['unit_price_ex_vat', 'vat_rate']);
    });
}

};
