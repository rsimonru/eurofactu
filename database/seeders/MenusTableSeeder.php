<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), "description" => '{"en": "Settings", "es": "Ajustes"}',
                "route" => "" ,"pmenus_id" => 1, "order" => 1, "deep" => 1, "type" => "route", "icon" => "la la-cog", "level" => 10, "group" => null, "group_description" => null, ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), "description" => '{"en": "Users management", "es": "Gestión usuarios"}',
                "route" => "/admin/users" ,"pmenus_id" => 1, "order" => 1, "deep" => 2, "type" => "route", "icon" => "la la-user", "level" => 10, "group" => null, "group_description" => null, ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), "description" => '{"en": "Groups management", "es": "Gestión grupos"}',
                "route" => "/admin/groups" ,"pmenus_id" => 1, "order" => 1, "deep" => 2, "type" => "route", "icon" => "la la-users", "level" => 10, "group" => null, "group_description" => null, ],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), "description" => '{"en": "Permissions management", "es": "Gestión permisos"}',
                "route" => "/admin/permissions" ,"pmenus_id" => 1, "order" => 1, "deep" => 2, "type" => "route", "icon" => "la la-key", "level" => 10, "group" => null, "group_description" => null, ],
            ["id" => 5, "created_at" => now(), "updated_at" => now(), "description" => '{"en": "Menus management", "es": "Gestión menús"}',
                "route" => "/admin/menus" ,"pmenus_id" => 1, "order" => 1, "deep" => 2, "type" => "route", "icon" => "la la-align-justify", "level" => 10, "group" => null, "group_description" => null, ],
            ["id" => 6, "created_at" => now(), "updated_at" => now(), "description" => '{"en": "Companies management", "es": "Gestión compañias"}',
                "route" => "/admin/companies" ,"pmenus_id" => 1, "order" => 1, "deep" => 2, "type" => "route", "icon" => "las la-building", "level" => 10, "group" => null, "group_description" => null, ],

            ["id" => 7, "created_at" => now(), "updated_at" => now(), "description" => '{"en": "Thirdparties", "es": "Terceros"}',
                "route" => "" ,"pmenus_id" => 7, "order" => 2, "deep" => 1, "type" => "route", "icon" => "la la-users", "level" => 2, "group" => null, "group_description" => null, ],
            ["id" => 8, "created_at" => now(), "updated_at" => now(), "description" => '{"en": "Management", "es": "Gestión"}',
                "route" => "/thirdparties/thirdparties" ,"pmenus_id" => 7, "order" => 2, "deep" => 2, "type" => "route", "icon" => "la la-users", "level" => 2, "group" => null, "group_description" => null, ],

            ["id" => 9, "created_at" => now(), "updated_at" => now(), "description" => '{"en": "Sales", "es": "Ventas"}',
                "route" => "" ,"pmenus_id" => 9, "order" => 3, "deep" => 1, "type" => "route", "icon" => "las la-store", "level" => 2, "group" => null, "group_description" => null, ],
            ["id" => 10, "created_at" => now(), "updated_at" => now(), "description" => '{"en": "Budgets", "es": "Presupuestos"}',
                "route" => "/sales/budgets" ,"pmenus_id" => 9, "order" => 2, "deep" => 2, "type" => "route", "icon" => "las la-paste", "level" => 2, "group" => null, "group_description" => null, ],
            ["id" => 11, "created_at" => now(), "updated_at" => now(), "description" => '{"en": "Notes", "es": "Albaranes"}',
                "route" => "/sales/notes" ,"pmenus_id" => 9, "order" => 3, "deep" => 2, "type" => "route", "icon" => "las la-file-invoice-dollar", "level" => 2, "group" => null, "group_description" => null, ],
            ["id" => 12, "created_at" => now(), "updated_at" => now(), "description" => '{"en": "Orders", "es": "Pedidos"}',
                "route" => "/sales/orders" ,"pmenus_id" => 9, "order" => 3, "deep" => 2, "type" => "route", "icon" => "las la-file-invoice-dollar", "level" => 2, "group" => null, "group_description" => null, ],
            ["id" => 13, "created_at" => now(), "updated_at" => now(), "description" => '{"en": "Invoices", "es": "Facturas"}',
                "route" => "/sales/invoices" ,"pmenus_id" => 9, "order" => 3, "deep" => 2, "type" => "route", "icon" => "las la-file-invoice-dollar", "level" => 2, "group" => null, "group_description" => null, ],

            ["id" => 14, "created_at" => now(), "updated_at" => now(), "description" => '{"en": "Reports", "es": "Informes"}',
                "route" => "" ,"pmenus_id" => 14, "order" => 4, "deep" => 1, "type" => "route", "icon" => "las la-file-invoice", "level" => 2, "group" => null, "group_description" => null, ],
            ["id" => 15, "created_at" => now(), "updated_at" => now(), "description" => '{"en": "Billing per client", "es": "Fact. por cliente"}',
                "route" => "/reports/invoices-by-customers" ,"pmenus_id" => 14, "order" => 2, "deep" => 2, "type" => "route", "icon" => "la la-users", "level" => 2, "group" => null, "group_description" => null, ],
    ];

        Menu::upsert($records, ['id'], ['description', 'route', 'pmenus_id', 'order', 'deep', 'type', 'group', 'group_description', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
