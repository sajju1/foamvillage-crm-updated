<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')->unique(); // digits-only
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_note_id')->nullable()->constrained()->nullOnDelete();

            $table->dateTime('issued_at');
            $table->date('due_date')->nullable();

            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('vat_amount', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2);

            $table->string('currency', 3)->default('GBP');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
