<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('assets', 'unit_measure')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('unit_measure')->nullable()->after('article');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('assets', 'unit_measure')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropColumn('unit_measure');
            });
        }
    }
};
