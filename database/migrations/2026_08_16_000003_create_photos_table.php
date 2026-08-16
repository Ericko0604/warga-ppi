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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('resident_id')->nullable()->constrained('residents')->cascadeOnDelete();
            $table->enum('uploader_type', ['ADMIN', 'RESIDENT'])->default('RESIDENT');
            $table->string('file_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->default('image/webp');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->integer('width')->default(0);
            $table->integer('height')->default(0);
            $table->timestamps();

            // Strictly enforce max 1 photo per resident per event at database level
            $table->unique(['event_id', 'resident_id']);
            $table->index(['event_id', 'uploader_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
