<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foam_types', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // White Foam, Blue Foam, etc.
            $table->string('calculation_method')->default('volume'); 
            $table->decimal('default_price_unit', 10, 4); // e.g. 0.6
            $table->decimal('default_cost_unit', 10, 4)->nullable();

            $table->enum('status', ['active', 'inactive'])
                ->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foam_types');
    }
};
