<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_lines', function (Blueprint $table) {

            // 1. Update line_status enum
            $table->enum('line_status', [
                'pending',
                'fulfilled',
                'cancelled',
            ])->default('pending')->change();
        });

        // 2. Fix product FK delete behaviour
        Schema::table('order_lines', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_lines', function (Blueprint $table) {

            // Revert enum (best-effort rollback)
            $table->enum('line_status', [
                'active',
                'cancelled',
            ])->default('active')->change();
        });

        Schema::table('order_lines', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }
};
