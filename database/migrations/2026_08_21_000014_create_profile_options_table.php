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
        Schema::create('profile_options', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // interest, dating_purpose, lifestyle, about_me
            $table->string('category')->nullable(); // Sport va Fitnes, Musiqa, Chekish odati...
            $table->string('name')->nullable();
            $table->string('icon')->nullable(); // Emoji yoki icon belgisi
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_options');
    }
};
