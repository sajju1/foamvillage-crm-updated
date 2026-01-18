<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('foam_pricing_rules', function (Blueprint $table) {
            $table->foreignId('foam_type_id')
                ->nullable()
                ->after('product_id')
                ->constrained('foam_types')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('foam_pricing_rules', function (Blueprint $table) {
            $table->dropForeign(['foam_type_id']);
            $table->dropColumn('foam_type_id');
        });
    }
};
