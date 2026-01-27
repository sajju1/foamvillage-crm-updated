<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')
                ->constrained()
                ->cascadeOnDelete();

            // Snapshot fields
            $table->string('description');
            $table->decimal('quantity', 10, 2);

            $table->decimal('unit_price_ex_vat', 10, 2);

            // VAT snapshot
            $table->decimal('vat_rate', 5, 2);
            $table->decimal('vat_amount', 10, 2);

            // Line total (inc VAT)
            $table->decimal('line_total_inc_vat', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_lines');
    }
};
