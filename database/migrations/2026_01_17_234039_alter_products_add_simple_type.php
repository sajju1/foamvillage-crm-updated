<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    DB::statement("
        ALTER TABLE products
        MODIFY product_type ENUM(
            'simple',
            'variant_based',
            'rule_based'
        ) NOT NULL DEFAULT 'simple'
    ");
}

public function down(): void
{
    DB::statement("
        ALTER TABLE products
        MODIFY product_type ENUM(
            'variant_based',
            'rule_based'
        ) NOT NULL
    ");
}

};
