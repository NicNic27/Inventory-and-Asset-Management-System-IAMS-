<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('ppe_sub_major_account_group', 2)->nullable()->after('acquisition_date');
            $table->string('general_ledger_account', 2)->nullable()->after('ppe_sub_major_account_group');
            $table->string('location_office', 2)->nullable()->after('general_ledger_account');
            $table->unsignedTinyInteger('set_sequence')->nullable()->after('location_office');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'ppe_sub_major_account_group',
                'general_ledger_account',
                'location_office',
                'set_sequence',
            ]);
        });
    }
};
