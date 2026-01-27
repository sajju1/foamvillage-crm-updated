<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->id();

            $table->string('credit_note_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            $table->dateTime('issued_at');
            $table->string('reason')->nullable();

            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('GBP');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_notes');
    }
};
