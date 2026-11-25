<?php

namespace Database\Seeders;

use App\Models\Preset;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PresetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Preset::factory(10000)->create();
    }
}
