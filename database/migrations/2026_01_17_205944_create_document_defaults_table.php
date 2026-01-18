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
        Schema::create('document_defaults', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->unsignedBigInteger('company_id');

            // Scope
            $table->enum('document_type', ['invoice', 'delivery_note', 'all'])->default('all');

            // Header configuration
            $table->enum('header_title_source', [
                'legal_company_name',
                'brand_name',
                'custom_text'
            ])->default('legal_company_name');

            $table->string('header_custom_text')->nullable();

            $table->enum('header_logo_source', [
                'company_logo',
                'brand_logo',
                'none'
            ])->default('company_logo');

            // Footer configuration
            $table->enum('footer_text_source', [
                'legal_disclosure',
                'custom_text',
                'none'
            ])->default('legal_disclosure');

            $table->text('footer_custom_text')->nullable();
            $table->text('legal_disclosure_text')->nullable();

            // Display toggles
            $table->boolean('show_address')->default(true);
            $table->boolean('show_company_number')->default(true);
            $table->boolean('show_vat_number')->default(true);
            $table->boolean('show_bank_details')->default(true);

            $table->timestamps();

            // Constraints
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->cascadeOnDelete();

            // One defaults record per company per document scope
            $table->unique(['company_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_defaults');
    }
};
