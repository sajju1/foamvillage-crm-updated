<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_status_history', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->enum('old_status', ['active', 'on_hold', 'blocked']);
            $table->enum('new_status', ['active', 'on_hold', 'blocked']);

            $table->text('reason');

            $table->foreignId('changed_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('changed_at')->useCurrent();

            // Indexes for audit and reporting
            $table->index('customer_id');
            $table->index('changed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_status_history');
    }
};
