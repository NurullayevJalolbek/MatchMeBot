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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->default('👑')->nullable();
            $table->integer('period_count')->default(1);
            $table->string('period_type')->default('month'); // day, week, month, year (Enum handled in PHP)
            $table->integer('days')->default(30);
            $table->decimal('price', 12, 2);
            $table->decimal('original_price', 12, 2)->nullable();
            $table->string('badge')->nullable();
            $table->json('features')->nullable(); // Array of [{icon, title, description}]
            $table->string('status')->default('active'); // active, inactive (Enum handled in PHP)
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
