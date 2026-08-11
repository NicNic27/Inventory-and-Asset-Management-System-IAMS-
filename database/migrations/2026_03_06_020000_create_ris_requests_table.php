<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ris_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ris_no')->unique();
            $table->string('entity_name')->nullable();
            $table->string('division')->nullable();
            $table->string('office')->nullable();
            $table->string('fund_cluster')->nullable();
            $table->string('rcc')->nullable();
            $table->text('purpose')->nullable();
            $table->string('sig_requested_by')->nullable();
            $table->string('sig_approved_by')->nullable();
            $table->string('sig_issued_by')->nullable();
            $table->string('sig_received_by')->nullable();
            $table->string('desig_requested')->nullable();
            $table->string('desig_approved')->nullable();
            $table->string('desig_issued')->nullable();
            $table->string('desig_received')->nullable();
            $table->date('date_requested')->nullable();
            $table->date('date_approved')->nullable();
            $table->date('date_issued')->nullable();
            $table->date('date_received')->nullable();
            $table->string('status')->default('Pending Staff Review');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ris_requests');
    }
};
