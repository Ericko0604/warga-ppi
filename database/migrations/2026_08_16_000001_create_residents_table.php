<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->string('block'); // 'A1', 'A2', 'A3', 'A4', 'KAVLING'
            $table->string('house_number')->nullable(); // '01', '02', ..., NULL for KAVLING
            $table->string('family_head_name')->nullable(); // Required for KAVLING, optional for house numbers
            $table->string('upload_token', 64)->unique();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();

            $table->index(['block', 'house_number']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
