<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->date('inventory_date')->nullable()->after('id');
            $table->string('model')->nullable()->after('description');
            $table->string('serial_number')->nullable()->after('model');
            $table->date('acquisition_date')->nullable()->after('serial_number');
            $table->string('person_accountable')->nullable()->after('unit_measure');
            $table->text('validation_signatory')->nullable()->after('person_accountable');
            $table->index('serial_number');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex(['assets_serial_number_index']);
            $table->dropColumn([
                'inventory_date',
                'model',
                'serial_number',
                'acquisition_date',
                'person_accountable',
                'validation_signatory',
            ]);
        });
    }
};
