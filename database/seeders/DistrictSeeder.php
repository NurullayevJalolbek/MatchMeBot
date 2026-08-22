<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/districts.json');
        if (!File::exists($path)) {
            $path = base_path('datas/district.json');
        }

        if (File::exists($path)) {
            $districts = json_decode(File::get($path), true);
            foreach ($districts as $d) {
                District::updateOrCreate(
                    ['id' => $d['id']],
                    [
                        'region_id' => $d['region_id'],
                        'name_uz' => $d['name_uz'],
                        'name_ru' => $d['name_ru'] ?? null,
                        'order' => $d['order'] ?? $d['id'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
