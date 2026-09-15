<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_items', 'dest_section')) {
                $table->string('dest_section')->nullable()->after('supply_id');
            }
            if (!Schema::hasColumn('purchase_order_items', 'dest_classification')) {
                $table->string('dest_classification')->nullable()->after('dest_section');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            foreach (['dest_classification', 'dest_section'] as $column) {
                if (Schema::hasColumn('purchase_order_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
