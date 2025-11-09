<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class LevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'name' => json_encode(['es' => 'Básico',   'en' => 'Basic']),             'level' => 1, ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'name' => json_encode(['es' => 'Colaborador', 'en' => 'Collaborator']),   'level' => 2, ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'name' => json_encode(['es' => 'Empleado',    'en' => 'Employee']),       'level' => 3, ],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), 'name' => json_encode(['es' => 'Responsable', 'en' => 'Responsable']),    'level' => 5, ],
            ["id" => 5, "created_at" => now(), "updated_at" => now(), 'name' => json_encode(['es' => 'Administrador', 'en' => 'Administrator']), 'level' => 10, ],
            ["id" => 6, "created_at" => now(), "updated_at" => now(), 'name' => json_encode(['es' => 'Superadmin',  'en' => 'Superadmin']),     'level' => 100, ],
        ];

        Level::upsert($records, ['id'], ['name', 'level', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
