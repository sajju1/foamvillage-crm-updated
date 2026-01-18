<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Match foam_pricing_rules.foam_type to foam_types.name
        DB::statement("
            UPDATE foam_pricing_rules fpr
            JOIN foam_types ft
              ON LOWER(TRIM(fpr.foam_type)) = LOWER(TRIM(ft.name))
            SET fpr.foam_type_id = ft.id
            WHERE fpr.foam_type_id IS NULL
              AND fpr.foam_type IS NOT NULL
        ");
    }

    public function down(): void
    {
        // We do NOT unset foam_type_id on rollback
        // because this is a one-way data improvement
    }
};
