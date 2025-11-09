<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), "name" => "THIRDPARTIES_CREATE",        "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Terceros - Crear", "en"=> "Thirdparties - Create"]), ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), "name" => "THIRDPARTIES_UPDATE",        "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Terceros - Modificar", "en"=> "Thirdparties - Update"]), ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), "name" => "THIRDPARTIES_DELETE",        "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Terceros - Eliminar", "en"=> "Thirdparties - Delete"]), ],

            ["id" => 4, "created_at" => now(), "updated_at" => now(), "name" => "INVOICES_CREATE",        "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Facturas - Crear", "en"=> "Invoices - Create"]), ],
            ["id" => 5, "created_at" => now(), "updated_at" => now(), "name" => "INVOICES_UPDATE",        "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Facturas - Modificar", "en"=> "Invoices - Update"]), ],
            ["id" => 6, "created_at" => now(), "updated_at" => now(), "name" => "INVOICES_DELETE",        "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Facturas - Eliminar", "en"=> "Invoices - Delete"]), ],

            ["id" => 7, "created_at" => now(), "updated_at" => now(), "name" => "BUDGETS_CREATE",        "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Presupuestos - Crear", "en"=> "Budgets - Create"]), ],
            ["id" => 8, "created_at" => now(), "updated_at" => now(), "name" => "BUDGETS_UPDATE",        "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Presupuestos - Modificar", "en"=> "Budgets - Update"]), ],
            ["id" => 9, "created_at" => now(), "updated_at" => now(), "name" => "BUDGETS_DELETE",        "guard_name" => "web", "class" => "permission", "model" => null, "model_id" => null, "data" => null, "level" => 2,
                "description" => json_encode(["es" => "Presupuestos - Eliminar", "en"=> "Budgets - Delete"]), ],
        ];

        $menus = Menu::all();
        foreach ($menus as $menu) {
            $records[] = [
                'id' => 1000+$menu->id,
                'created_at' => $menu->created_at,
                'updated_at' => $menu->updated_at,
                'name' => 'Menu '. $menu->description . ' ' .$menu->id,
                'guard_name' => 'web',
                'class' => 'model',
                'model' => Menu::class,
                'model_id' => $menu->id,
                'data' => null,
                'level' => $menu->level,
                'description' => json_encode($menu->getTranslations()['description']),
            ];
        }

        $result = Permission::upsert($records, ['name', 'guard_name'], ['id', 'description', 'class', 'model', 'model_id', 'data', 'level', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
