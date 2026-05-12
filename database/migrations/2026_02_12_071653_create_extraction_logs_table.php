<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('extraction_logs', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->date('filter_date')->nullable();
            $table->string('extraction_type')->default('Inpatient');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extraction_logs');
    }
};
