<?php

namespace Database\Seeders;

use App\Models\StatesModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class StatesModelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'states_id' => 1, 'model' => 'App\Models\SalesInvoice', 'order' => 4, ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'states_id' => 2, 'model' => 'App\Models\SalesInvoice', 'order' => 2, ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'states_id' => 3, 'model' => 'App\Models\SalesInvoice', 'order' => 1, ],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), 'states_id' => 9, 'model' => 'App\Models\SalesInvoice', 'order' => 3, ],

            ["id" => 6, "created_at" => now(), "updated_at" => now(), 'states_id' => 2, 'model' => 'App\Models\SalesBudget', 'order' => 1, ],
            ["id" => 8, "created_at" => now(), "updated_at" => now(), 'states_id' => 8, 'model' => 'App\Models\SalesBudget', 'order' => 2, ],
            ["id" => 9, "created_at" => now(), "updated_at" => now(), 'states_id' => 9, 'model' => 'App\Models\SalesBudget', 'order' => 3, ],

        ];

        StatesModel::upsert($records, ['id'], ['states_id', 'model', 'order', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
