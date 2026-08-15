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
        Schema::create('user_filters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('looking_for', 20)->default('female')->index();
            $table->unsignedSmallInteger('min_age')->default(18)->index();
            $table->unsignedSmallInteger('max_age')->default(28)->index();
            $table->unsignedSmallInteger('max_distance_km')->default(50);
            $table->string('city', 50)->default('all')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_filters');
    }
};
