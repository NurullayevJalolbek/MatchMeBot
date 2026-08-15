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
        Schema::create('model_files', function (Blueprint $table) {
            $table->id();
            $table->morphs('model'); // Creates model_type and model_id (indexed)
            $table->string('file_path');
            $table->string('file_type', 50)->default('photo')->index();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('is_main')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_files');
    }
};
