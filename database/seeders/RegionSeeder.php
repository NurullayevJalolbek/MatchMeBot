<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/regions.json');
        if (!File::exists($path)) {
            $path = base_path('datas/region.json');
        }

        if (File::exists($path)) {
            $regions = json_decode(File::get($path), true);
            foreach ($regions as $r) {
                Region::updateOrCreate(
                    ['id' => $r['id']],
                    [
                        'name_uz' => $r['name_uz'],
                        'name_ru' => $r['name_ru'] ?? null,
                        'order' => $r['order'] ?? $r['id'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
