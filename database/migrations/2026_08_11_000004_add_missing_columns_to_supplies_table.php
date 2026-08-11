<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplies', function (Blueprint $table) {
            if (!Schema::hasColumn('supplies', 'description')) {
                $table->text('description')->nullable()->after('article');
            }
            if (!Schema::hasColumn('supplies', 'supplier')) {
                $table->string('supplier')->nullable()->after('description');
            }
            if (!Schema::hasColumn('supplies', 'unit_measure')) {
                $table->string('unit_measure')->nullable()->after('barcode_id');
            }
            if (!Schema::hasColumn('supplies', 'unit_value')) {
                $table->decimal('unit_value', 15, 2)->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('supplies', 'status')) {
                $table->string('status')->default('Available')->after('unit_value');
            }
            if (!Schema::hasColumn('supplies', 'image')) {
                $table->string('image')->nullable()->after('status');
            }
            if (!Schema::hasColumn('supplies', 'low_stock_threshold')) {
                $table->integer('low_stock_threshold')->nullable()->after('quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('supplies', function (Blueprint $table) {
            if (Schema::hasColumn('supplies', 'low_stock_threshold')) {
                $table->dropColumn('low_stock_threshold');
            }
            if (Schema::hasColumn('supplies', 'image')) {
                $table->dropColumn('image');
            }
            if (Schema::hasColumn('supplies', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('supplies', 'unit_value')) {
                $table->dropColumn('unit_value');
            }
            if (Schema::hasColumn('supplies', 'unit_measure')) {
                $table->dropColumn('unit_measure');
            }
            if (Schema::hasColumn('supplies', 'supplier')) {
                $table->dropColumn('supplier');
            }
            if (Schema::hasColumn('supplies', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
