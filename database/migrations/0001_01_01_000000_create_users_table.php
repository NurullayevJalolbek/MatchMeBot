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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('telegram_id')->unique()->nullable()->index();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->nullable()->index();
            $table->string('language_code', 10)->default('uz');
            $table->string('name')->nullable();
            $table->date('birth_date')->nullable();
            $table->unsignedSmallInteger('age')->nullable()->index();
            $table->string('gender', 10)->nullable()->index();
            $table->string('looking_for', 10)->nullable()->index();
            $table->string('city', 100)->nullable()->index();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('bio', 250)->nullable();
            $table->boolean('onboarding_completed')->default(false)->index();
            $table->decimal('balance', 12, 2)->default(0);
            $table->unsignedInteger('daily_streak')->default(0);
            $table->boolean('is_vip')->default(false)->index();
            $table->timestamp('vip_expires_at')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->boolean('is_terms_accepted')->default(false);
            $table->timestamp('terms_accepted_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
