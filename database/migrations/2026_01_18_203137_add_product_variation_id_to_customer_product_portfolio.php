<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('customer_product_portfolio', function (Blueprint $table) {

            // Add column ONLY if it doesn't exist
            if (!Schema::hasColumn('customer_product_portfolio', 'product_variation_id')) {
                $table->foreignId('product_variation_id')
                    ->nullable()
                    ->after('product_id')
                    ->constrained('product_variations')
                    ->nullOnDelete();
            }

            // Add composite index with short name (if not exists)
            $table->index(
                ['customer_id', 'product_id', 'product_variation_id'],
                'cpp_customer_product_variation_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('customer_product_portfolio', function (Blueprint $table) {
            $table->dropIndex('cpp_customer_product_variation_idx');

            if (Schema::hasColumn('customer_product_portfolio', 'product_variation_id')) {
                $table->dropForeign(['product_variation_id']);
                $table->dropColumn('product_variation_id');
            }
        });
    }
};
