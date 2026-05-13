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
        Schema::table('ics_requests', function (Blueprint $table) {
            $table->string('signed_document')->nullable()->after('items_json');
        });
    }

    public function down(): void
    {
        Schema::table('ics_requests', function (Blueprint $table) {
            $table->dropColumn('signed_document');
        });
    }
};
