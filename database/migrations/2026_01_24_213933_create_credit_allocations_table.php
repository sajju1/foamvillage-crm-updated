<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('credit_allocations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('credit_note_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();

            $table->decimal('amount_applied', 10, 2);
            $table->dateTime('applied_at');

            $table->string('notes')->nullable();

            $table->timestamps();

            $table->unique(
                ['credit_note_id', 'invoice_id', 'amount_applied'],
                'credit_allocations_unique_guard'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_allocations');
    }
};
