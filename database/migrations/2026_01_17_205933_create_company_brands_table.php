<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_brands', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->unsignedBigInteger('company_id');

            // Brand identity
            $table->string('brand_name');
            $table->unsignedBigInteger('logo_file_id')->nullable();

            // Optional brand contact details
            $table->string('brand_email')->nullable();
            $table->string('brand_phone')->nullable();
            $table->string('brand_website')->nullable();

            // System flags
            $table->boolean('is_default_brand')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();

            // Constraints
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->cascadeOnDelete();

            $table->foreign('logo_file_id')
                ->references('id')
                ->on('files')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_brands');
    }
};
