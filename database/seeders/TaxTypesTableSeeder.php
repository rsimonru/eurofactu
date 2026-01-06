<?php

namespace Database\Seeders;

use App\Models\TaxType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TaxTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'tax_id' => 1, 'type' => 'Exento', 'value' => 0, 'pes' => 0, ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'tax_id' => 1, 'type' => '4%', 'value' => 0.04, 'pes' => 0.005 ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'tax_id' => 1, 'type' => '10%', 'value' => 0.10, 'pes' => 0.014 ],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), 'tax_id' => 1, 'type' => '21%', 'value' => 0.21, 'pes' => 0.052 ],
        ];

        TaxType::upsert($records, ['id'], ['tax_id', 'type', 'value', 'pes', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
