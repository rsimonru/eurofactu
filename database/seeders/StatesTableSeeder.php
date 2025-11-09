<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Pagado', 'en' => 'Paid']), 'color' => '00a65a', 'class' => 'success', 'code' => 'paid',],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Pendiente', 'en' => 'Pending']), 'color' => 'f39c12', 'class' => 'warning', 'code' => 'pending',],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Abierto', 'en' => 'Open']), 'color' => '00a65a', 'class' => 'success', 'code' => 'open',],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Cerrado', 'en' => 'Closed']), 'color' => 'f56954', 'class' => 'danger', 'code' => 'closed',],
            ["id" => 5, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Cancelado', 'en' => 'Cancelled']), 'color' => 'f56954', 'class' => 'danger', 'code' => 'cancelled',],
            ["id" => 6, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Activo', 'en' => 'Active']), 'color' => '00a65a', 'class' => 'success', 'code' => 'active',],
            ["id" => 7, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Inactivo', 'en' => 'Inactive']), 'color' => 'f56954', 'class' => 'danger', 'code' => 'inactive',],
            ["id" => 8, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Aprobado', 'en' => 'Approved']), 'color' => '00a65a', 'class' => 'success', 'code' => 'approved',],
            ["id" => 9, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Enviado', 'en' => 'Sent']), 'color' => 'f39c12', 'class' => 'warning', 'code' => 'sent',],
        ];

        State::upsert($records, ['id'], ['description', 'color', 'class', 'code', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
