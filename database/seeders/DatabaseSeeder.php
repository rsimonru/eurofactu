<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LevelsTableSeeder::class);
        $this->call(MenusTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(ProvincesTableSeeder::class);
        $this->call(CountriesSeeder::class);
        $this->call(StatesModelsTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(TaxTypesTableSeeder::class);
    }
}
