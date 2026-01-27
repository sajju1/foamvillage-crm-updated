<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoice_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_lines', 'note')) {
                $table->text('note')->nullable()->after('description');
            }

            if (!Schema::hasColumn('invoice_lines', 'source')) {
                $table->string('source', 50)->nullable()->after('note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoice_lines', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_lines', 'note')) {
                $table->dropColumn('note');
            }

            if (Schema::hasColumn('invoice_lines', 'source')) {
                $table->dropColumn('source');
            }
        });
    }
};
