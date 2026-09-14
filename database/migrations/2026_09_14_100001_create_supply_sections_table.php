<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // Supply Section, e.g. "Bond Paper"
            $table->string('classification');  // Classification, e.g. "A4"
            $table->timestamps();

            $table->unique(['name', 'classification']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_sections');
    }
};
