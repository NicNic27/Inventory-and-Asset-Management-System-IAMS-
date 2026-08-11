<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('transactions', 'supplier')) {
                    $table->string('supplier')->nullable()->after('quantity');
                }

                if (!Schema::hasColumn('transactions', 'transaction_date')) {
                    $table->date('transaction_date')->nullable()->after('supplier');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (Schema::hasColumn('transactions', 'transaction_date')) {
                    $table->dropColumn('transaction_date');
                }

                if (Schema::hasColumn('transactions', 'supplier')) {
                    $table->dropColumn('supplier');
                }
            });
        }
    }
};
