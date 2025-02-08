<?php

namespace Database\Seeders;

use App\Models\Preference;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PreferencesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Preference::create([
            'user_id' => 1, 
            'preferred_sources' => json_encode(['9to5Toys', 'Polygon']),
            'preferred_categories' => json_encode(['technology', 'general']),
            'preferred_authors' => json_encode(['Guardian']),
        ]);

    }
}
