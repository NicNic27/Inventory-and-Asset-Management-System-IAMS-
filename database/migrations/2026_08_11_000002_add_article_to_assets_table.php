<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('assets', 'article')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('article')->nullable()->after('item_code');
            });

            if (Schema::hasColumn('assets', 'name')) {
                DB::table('assets')->update(['article' => DB::raw('name')]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('assets', 'article')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropColumn('article');
            });
        }
    }
};
