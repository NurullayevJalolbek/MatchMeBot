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
        Schema::table('boost_plans', function (Blueprint $table) {
            
            if (!Schema::hasColumn('boost_plans', 'title')) {
                $table->string('title')->nullable()->after('id');
            }
            if (!Schema::hasColumn('boost_plans', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('boost_plans', 'status')) {
                $table->string('status')->default('active')->after('badge_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boost_plans', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'status']);
        });
    }
};
