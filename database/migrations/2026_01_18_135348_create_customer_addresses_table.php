<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->enum('address_type', ['registered', 'billing', 'delivery']);

            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('address_line3')->nullable();
            $table->string('city');
            $table->string('state_region')->nullable();
            $table->string('country');
            $table->string('postcode');

            // Delivery-specific flags
            $table->boolean('is_default')->default(false);

            // Lifecycle control (no deletes)
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('deactivated_at')->nullable();

            // Indexes for fast lookups
            $table->index(['customer_id', 'address_type']);
            $table->index(['customer_id', 'address_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
