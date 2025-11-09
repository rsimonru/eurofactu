<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProvincesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $records = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'province' =>'Álava','code' =>'1', ],
            ["id" => 2, "created_at" => now(), "updated_at" => now(), 'province' =>'Albacete','code' =>'2', ],
            ["id" => 3, "created_at" => now(), "updated_at" => now(), 'province' =>'Alicante','code' =>'3', ],
            ["id" => 4, "created_at" => now(), "updated_at" => now(), 'province' =>'Almería','code' =>'4', ],
            ["id" => 5, "created_at" => now(), "updated_at" => now(), 'province' =>'Ávila','code' =>'5', ],
            ["id" => 6, "created_at" => now(), "updated_at" => now(), 'province' =>'Badajoz','code' =>'6', ],
            ["id" => 7, "created_at" => now(), "updated_at" => now(), 'province' =>'Baleares','code' =>'7', ],
            ["id" => 8, "created_at" => now(), "updated_at" => now(), 'province' =>'Barcelona','code' =>'8', ],
            ["id" => 9, "created_at" => now(), "updated_at" => now(), 'province' =>'Burgos','code' =>'9', ],
            ["id" => 10, "created_at" => now(), "updated_at" => now(), 'province' =>'Cáceres','code' =>'10', ],
            ["id" => 11, "created_at" => now(), "updated_at" => now(), 'province' =>'Cádiz','code' =>'11', ],
            ["id" => 12, "created_at" => now(), "updated_at" => now(), 'province' =>'Castellón','code' =>'12', ],
            ["id" => 13, "created_at" => now(), "updated_at" => now(), 'province' =>'Ciudad Real','code' =>'13', ],
            ["id" => 14, "created_at" => now(), "updated_at" => now(), 'province' =>'Córdoba','code' =>'15', ],
            ["id" => 15, "created_at" => now(), "updated_at" => now(), 'province' =>'A Coruña','code' =>'15', ],
            ["id" => 16, "created_at" => now(), "updated_at" => now(), 'province' =>'Cuenca','code' =>'16', ],
            ["id" => 17, "created_at" => now(), "updated_at" => now(), 'province' =>'Girona','code' =>'17', ],
            ["id" => 18, "created_at" => now(), "updated_at" => now(), 'province' =>'Granada','code' =>'18', ],
            ["id" => 19, "created_at" => now(), "updated_at" => now(), 'province' =>'Guadalajara','code' =>'19', ],
            ["id" => 20, "created_at" => now(), "updated_at" => now(), 'province' =>'Gipuzkoa','code' =>'20', ],
            ["id" => 21, "created_at" => now(), "updated_at" => now(), 'province' =>'Huelva','code' =>'21', ],
            ["id" => 22, "created_at" => now(), "updated_at" => now(), 'province' =>'Huesca','code' =>'22', ],
            ["id" => 23, "created_at" => now(), "updated_at" => now(), 'province' =>'Jaén','code' =>'23', ],
            ["id" => 24, "created_at" => now(), "updated_at" => now(), 'province' =>'León','code' =>'24', ],
            ["id" => 25, "created_at" => now(), "updated_at" => now(), 'province' =>'Lérida','code' =>'25', ],
            ["id" => 26, "created_at" => now(), "updated_at" => now(), 'province' =>'La Rioja','code' =>'26', ],
            ["id" => 27, "created_at" => now(), "updated_at" => now(), 'province' =>'Lugo','code' =>'27', ],
            ["id" => 28, "created_at" => now(), "updated_at" => now(), 'province' =>'Madrid','code' =>'28', ],
            ["id" => 29, "created_at" => now(), "updated_at" => now(), 'province' =>'Málaga','code' =>'29', ],
            ["id" => 30, "created_at" => now(), "updated_at" => now(), 'province' =>'Murcia','code' =>'30', ],
            ["id" => 31, "created_at" => now(), "updated_at" => now(), 'province' =>'Navarra','code' =>'31', ],
            ["id" => 32, "created_at" => now(), "updated_at" => now(), 'province' =>'Ourense','code' =>'32', ],
            ["id" => 33, "created_at" => now(), "updated_at" => now(), 'province' =>'Asturias','code' =>'33', ],
            ["id" => 34, "created_at" => now(), "updated_at" => now(), 'province' =>'Palencia','code' =>'36', ],
            ["id" => 35, "created_at" => now(), "updated_at" => now(), 'province' =>'Las Palmas','code' =>'35', ],
            ["id" => 36, "created_at" => now(), "updated_at" => now(), 'province' =>'Pontevedra','code' =>'36', ],
            ["id" => 37, "created_at" => now(), "updated_at" => now(), 'province' =>'Salamanca','code' =>'37', ],
            ["id" => 38, "created_at" => now(), "updated_at" => now(), 'province' =>'Sta. Cruz Tenerife','code' =>'38', ],
            ["id" => 39, "created_at" => now(), "updated_at" => now(), 'province' =>'Cantabria','code' =>'39', ],
            ["id" => 40, "created_at" => now(), "updated_at" => now(), 'province' =>'Segovia','code' =>'40', ],
            ["id" => 41, "created_at" => now(), "updated_at" => now(), 'province' =>'Sevilla','code' =>'41', ],
            ["id" => 42, "created_at" => now(), "updated_at" => now(), 'province' =>'Soria','code' =>'42', ],
            ["id" => 43, "created_at" => now(), "updated_at" => now(), 'province' =>'Tarragona','code' =>'43', ],
            ["id" => 44, "created_at" => now(), "updated_at" => now(), 'province' =>'Teruel','code' =>'44', ],
            ["id" => 45, "created_at" => now(), "updated_at" => now(), 'province' =>'Toledo','code' =>'45', ],
            ["id" => 46, "created_at" => now(), "updated_at" => now(), 'province' =>'Valencia','code' =>'46', ],
            ["id" => 47, "created_at" => now(), "updated_at" => now(), 'province' =>'Valladolid','code' =>'47', ],
            ["id" => 48, "created_at" => now(), "updated_at" => now(), 'province' =>'Vizcaya','code' =>'48', ],
            ["id" => 49, "created_at" => now(), "updated_at" => now(), 'province' =>'Zamora','code' =>'49', ],
            ["id" => 50, "created_at" => now(), "updated_at" => now(), 'province' =>'Zaragoza','code' =>'50', ],
            ["id" => 51, "created_at" => now(), "updated_at" => now(), 'province' =>'Ceuta','code' =>'51', ],
            ["id" => 52, "created_at" => now(), "updated_at" => now(), 'province' =>'Melilla','code' =>'52', ],
        ];

        Province::upsert($records, ['id'], ['province', 'code', 'created_at', 'updated_at']);

        Schema::enableForeignKeyConstraints();
    }
}
