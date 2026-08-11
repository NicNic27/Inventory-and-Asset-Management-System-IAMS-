<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ris_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ris_id')->constrained('ris_requests')->cascadeOnDelete();
            $table->string('stock_no')->nullable();
            $table->string('unit')->nullable();
            $table->text('description')->nullable();
            $table->integer('req_quantity')->nullable();
            $table->string('stock_avail')->nullable();
            $table->integer('issue_quantity')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ris_items');
    }
};
