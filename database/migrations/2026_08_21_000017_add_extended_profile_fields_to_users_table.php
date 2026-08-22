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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'height')) {
                $table->smallInteger('height')->nullable()->after('age');
            }
            if (!Schema::hasColumn('users', 'weight')) {
                $table->smallInteger('weight')->nullable()->after('height');
            }
            if (!Schema::hasColumn('users', 'living_region_id')) {
                $table->foreignId('living_region_id')->nullable()->after('city')->constrained('regions')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'living_district_id')) {
                $table->foreignId('living_district_id')->nullable()->after('living_region_id')->constrained('districts')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'birth_region_id')) {
                $table->foreignId('birth_region_id')->nullable()->after('living_district_id')->constrained('regions')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'birth_district_id')) {
                $table->foreignId('birth_district_id')->nullable()->after('birth_region_id')->constrained('districts')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'profile_completion_percentage')) {
                $table->smallInteger('profile_completion_percentage')->default(0)->after('is_verified');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['living_region_id']);
            $table->dropForeign(['living_district_id']);
            $table->dropForeign(['birth_region_id']);
            $table->dropForeign(['birth_district_id']);
            $table->dropColumn([
                'height',
                'weight',
                'living_region_id',
                'living_district_id',
                'birth_region_id',
                'birth_district_id',
                'profile_completion_percentage',
            ]);
        });
    }
};
