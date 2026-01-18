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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // Legal identity
            $table->string('legal_name');
            $table->string('company_number')->nullable();
            $table->string('vat_number')->nullable();

            // Address
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('address_line3')->nullable();
            $table->string('city');
            $table->string('state_region')->nullable();
            $table->string('country');
            $table->string('postcode');

            // Contact
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('website')->nullable();

            // Logo
            $table->unsignedBigInteger('logo_file_id')->nullable();

            // Banking details
            $table->string('bank_account_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_sort_code')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_iban')->nullable();
            $table->string('bank_swift_bic')->nullable();

            // System flags
            $table->boolean('is_default')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();

            // Constraints
            $table->foreign('logo_file_id')->references('id')->on('files')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
