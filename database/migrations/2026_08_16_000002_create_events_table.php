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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->enum('type', ['ACARA', 'KEGIATAN'])->default('ACARA');
            $table->string('name');
            $table->date('event_date');
            $table->text('description')->nullable();
            $table->boolean('allow_resident_upload')->default(true);
            $table->enum('status', ['DRAFT', 'PUBLISHED', 'ARCHIVED'])->default('PUBLISHED');
            $table->string('thumbnail_path')->nullable();
            $table->timestamps();

            $table->index('event_date');
            $table->index(['status', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
