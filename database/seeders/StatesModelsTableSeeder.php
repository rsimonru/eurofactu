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
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'states_id' => 10, 'model' => 'App\Models\SalesInvoice', 'order' => 5, ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'states_id' => 2, 'model' => 'App\Models\SalesInvoice', 'order' => 3, ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'states_id' => 3, 'model' => 'App\Models\SalesInvoice', 'order' => 2, ],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), 'states_id' => 9, 'model' => 'App\Models\SalesInvoice', 'order' => 4, ],
            ["id" => 5, "created_at" => now(), "updated_at" => now(), 'states_id' => 12, 'model' => 'App\Models\SalesInvoice', 'order' => 1, ],

            ["id" => 6, "created_at" => now(), "updated_at" => now(), 'states_id' => 2, 'model' => 'App\Models\SalesBudget', 'order' => 1, ],
            ["id" => 7, "created_at" => now(), "updated_at" => now(), 'states_id' => 8, 'model' => 'App\Models\SalesBudget', 'order' => 2, ],
            ["id" => 8, "created_at" => now(), "updated_at" => now(), 'states_id' => 9, 'model' => 'App\Models\SalesBudget', 'order' => 3, ],

            ["id" => 9, "created_at" => now(), "updated_at" => now(), 'states_id' => 3, 'model' => 'App\Models\SalesOrder', 'order' => 1, ],
            ["id" => 10, "created_at" => now(), "updated_at" => now(), 'states_id' => 4, 'model' => 'App\Models\SalesOrder', 'order' => 2, ],
            ["id" => 11, "created_at" => now(), "updated_at" => now(), 'states_id' => 5, 'model' => 'App\Models\SalesOrder', 'order' => 3, ],

            ["id" => 12, "created_at" => now(), "updated_at" => now(), 'states_id' => 3, 'model' => 'App\Models\SalesNote', 'order' => 1, ],
            ["id" => 13, "created_at" => now(), "updated_at" => now(), 'states_id' => 13, 'model' => 'App\Models\SalesNote', 'order' => 2, ],
            ["id" => 14, "created_at" => now(), "updated_at" => now(), 'states_id' => 9, 'model' => 'App\Models\SalesNote', 'order' => 3, ],
            ["id" => 15, "created_at" => now(), "updated_at" => now(), 'states_id' => 11, 'model' => 'App\Models\SalesNote', 'order' => 4, ],
        ];

        StatesModel::upsert($records, ['id'], ['states_id', 'model', 'order', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
