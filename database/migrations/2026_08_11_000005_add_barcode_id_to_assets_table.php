<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('assets', 'barcode_id')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('barcode_id')->nullable()->after('item_code');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('assets', 'barcode_id')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropColumn('barcode_id');
            });
        }
    }
};
