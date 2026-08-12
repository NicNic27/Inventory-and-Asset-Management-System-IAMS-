<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('assets', 'unit_value')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->decimal('unit_value', 15, 2)->default(0)->after('unit_measure');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('assets', 'unit_value')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropColumn('unit_value');
            });
        }
    }
};
