<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('item_id')->nullable();
                $table->string('item_type')->nullable(); // 'assets' or 'supplies'
                $table->string('transaction_type')->nullable(); // IN/OUT/Added/etc
                $table->integer('quantity')->default(0);
                $table->text('remarks')->nullable();
                $table->dateTime('date_time')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
