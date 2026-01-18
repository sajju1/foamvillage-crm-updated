<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   
    /**
     * Reverse the migrations.
     */
    public function up(): void
{
    Schema::create('files', function (Blueprint $table) {
        $table->id();

        // File identity
        $table->string('file_name');
        $table->string('file_type')->nullable(); // e.g. image/png
        $table->string('file_path');

        // Who uploaded (user id, nullable for system)
        $table->unsignedBigInteger('uploaded_by')->nullable();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('files');
}

};
