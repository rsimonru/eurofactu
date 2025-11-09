<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialUserDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ["id" => 1, "created_at" => now(), "updated_at" => now(), 'name' => 'sistema', 'email' => 'sistema@euromatica.es', 'password' => Hash::make('$Root1001'), 'active' => 1, 'company_id' => null, 'level_id' => 6,],
        ];
        User::upsert($users, ['id'], ['name', 'email', 'password', 'active', 'company_id', 'level_id',  'created_at', 'updated_at']);

        $user = User::find(1);
        $user->givePermissionTo(1001);
        $user->givePermissionTo(1002);

    }
}
