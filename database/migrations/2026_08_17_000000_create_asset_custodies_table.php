<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_custodies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_type');
            $table->string('holder_name')->nullable();
            $table->string('holder_position')->nullable();
            $table->string('department')->nullable();
            $table->string('unit')->nullable();
            $table->date('issued_at')->nullable();
            $table->date('due_at')->nullable();
            $table->date('returned_at')->nullable();
            $table->string('condition_on_issue')->nullable();
            $table->string('condition_on_return')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['asset_id', 'returned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_custodies');
    }
};