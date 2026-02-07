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
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Pagado', 'en' => 'Paid']), 'color' => 'green', 'code' => 'paid',],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Pendiente', 'en' => 'Pending']), 'color' => 'yellow', 'code' => 'pending',],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Abierto', 'en' => 'Open']), 'color' => 'blue', 'code' => 'open',],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Cerrado', 'en' => 'Closed']), 'color' => 'red', 'code' => 'closed',],
            ["id" => 5, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Cancelado', 'en' => 'Cancelled']), 'color' => 'red', 'code' => 'cancelled',],
            ["id" => 6, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Activo', 'en' => 'Active']), 'color' => 'green', 'code' => 'active',],
            ["id" => 7, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Inactivo', 'en' => 'Inactive']), 'color' => 'red', 'code' => 'inactive',],
            ["id" => 8, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Aprobado', 'en' => 'Approved']), 'color' => 'green', 'code' => 'approved',],
            ["id" => 9, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Enviado', 'en' => 'Sent']), 'color' => 'blue', 'code' => 'sent',],
            ["id" => 10, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Cobrado', 'en' => 'Charged']), 'color' => 'green', 'code' => 'charged',],
            ["id" => 11, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Entregado', 'en' => 'Delivered']), 'color' => 'green', 'code' => 'delivered',],
            ["id" => 12, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Borrador', 'en' => 'Draft']), 'color' => 'zinc', 'code' => 'draft',],
            ["id" => 13, "created_at" => now(), "updated_at" => now(), 'description' => json_encode(['es' => 'Preparado', 'en' => 'Prepared']), 'color' => 'blue', 'code' => 'prepared',],

        ];

        State::upsert($records, ['id'], ['description', 'color', 'code', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
