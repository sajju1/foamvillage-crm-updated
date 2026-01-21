<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Core ownership
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            // Order identity
            $table->string('order_number')->unique();

            // Intake source
            $table->enum('source_channel', [
                'staff_intake',
                'customer_portal',
            ])->default('staff_intake');

            // Order lifecycle status (Order Sheet only)
            $table->enum('status', [
                'draft',
                'submitted',
                'acknowledged',
                'in_progress',
                'partially_fulfilled',
                'fulfilled',
                'cancelled',
                'closed',
            ])->default('draft');

            // Lifecycle timestamps
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();

            // Internal-only notes (never printed)
            $table->text('internal_notes')->nullable();

            // Staff user who created the order
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Indexes
            $table->index(['company_id', 'customer_id']);
            $table->index(['company_id', 'status']);
            $table->index('order_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
