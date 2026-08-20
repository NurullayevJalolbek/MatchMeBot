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
        if (Schema::hasTable('boost_plans') && !Schema::hasColumn('boost_plans', 'income_category_id')) {
            Schema::table('boost_plans', function (Blueprint $table) {
                $table->foreignId('income_category_id')->nullable()->after('id')->constrained('income_categories')->nullOnDelete();
            });
        }

        if (Schema::hasTable('subscription_plans') && !Schema::hasColumn('subscription_plans', 'income_category_id')) {
            Schema::table('subscription_plans', function (Blueprint $table) {
                $table->foreignId('income_category_id')->nullable()->after('id')->constrained('income_categories')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('boost_plans') && Schema::hasColumn('boost_plans', 'income_category_id')) {
            Schema::table('boost_plans', function (Blueprint $table) {
                $table->dropConstrainedForeignId('income_category_id');
            });
        }

        if (Schema::hasTable('subscription_plans') && Schema::hasColumn('subscription_plans', 'income_category_id')) {
            Schema::table('subscription_plans', function (Blueprint $table) {
                $table->dropConstrainedForeignId('income_category_id');
            });
        }
    }
};
