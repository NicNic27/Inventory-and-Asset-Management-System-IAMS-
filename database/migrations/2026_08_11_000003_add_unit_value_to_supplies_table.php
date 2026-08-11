<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('supplies', 'unit_value')) {
            Schema::table('supplies', function (Blueprint $table) {
                $table->decimal('unit_value', 15, 2)->default(0)->after('quantity');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('supplies', 'unit_value')) {
            Schema::table('supplies', function (Blueprint $table) {
                $table->dropColumn('unit_value');
            });
        }
    }
};
