<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vat_rules', function (Blueprint $table) {
            $table->id();

            // Human readable name (Standard, Reduced, Zero)
            $table->string('name');

            // VAT percentage rate (e.g. 20.00)
            $table->decimal('rate', 5, 2);

            // Only one rule should ever be default
            $table->boolean('is_default')->default(false);

            // Soft control: active / inactive (never delete VAT rules)
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Safety: prevent duplicate VAT names
            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vat_rules');
    }
};
