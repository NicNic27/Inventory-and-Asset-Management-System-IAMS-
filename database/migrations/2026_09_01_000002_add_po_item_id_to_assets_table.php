<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'po_item_id')) {
                // Links this asset unit back to the PO line it was delivered against
                $table->foreignId('po_item_id')->nullable()->after('id')
                    ->constrained('purchase_order_items')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'po_item_id')) {
                $table->dropConstrainedForeignId('po_item_id');
            }
        });
    }
};
