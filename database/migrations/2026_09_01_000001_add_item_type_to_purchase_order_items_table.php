<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_items', 'item_type')) {
                $table->enum('item_type', ['supply', 'asset'])->default('supply')->after('purchase_order_id');
            }
        });

        // Backfill existing rows from their parent PO's po_type so old POs keep working
        DB::table('purchase_order_items as poi')
            ->join('purchase_orders as po', 'po.id', '=', 'poi.purchase_order_id')
            ->where('po.po_type', 'Asset')
            ->update(['poi.item_type' => 'asset']);
    }

    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_order_items', 'item_type')) {
                $table->dropColumn('item_type');
            }
        });
    }
};
