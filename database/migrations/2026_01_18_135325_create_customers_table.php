<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Company ownership
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            // Account identity
            $table->string('account_number', 6)->unique();

            // Core contact details
            $table->string('contact_name');
            $table->string('email');
            $table->string('primary_phone');
            $table->string('secondary_phone')->nullable();

            // Business identity (optional)
            $table->string('registered_company_name')->nullable();
            $table->string('vat_number')->nullable();

            // Customer status
            $table->enum('customer_status', ['active', 'on_hold', 'blocked'])
                ->default('active');

            // Financial controls
            $table->decimal('credit_limit', 12, 2)->nullable();
            $table->enum('payment_terms', [
                'immediate',
                '7_days',
                '14_days',
                '30_days',
                'custom'
            ])->default('immediate');

            // Restricted / internal fields
            $table->text('internal_notes')->nullable();
            $table->foreignId('account_manager_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Indexes
            $table->index(['company_id', 'customer_status']);
            $table->index('account_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
