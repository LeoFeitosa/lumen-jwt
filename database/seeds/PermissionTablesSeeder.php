<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PermissionTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // REGRAS
        DB::table('roles')->insert([
            [
                'name' => 'super-admin',
                'guard_name' => 'api',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'admin',
                'guard_name' => 'api',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'user',
                'guard_name' => 'api',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        ]);

        // PERMISSÕES
        DB::table('permissions')->insert([
            [
                'name' => 'get',
                'guard_name' => 'api',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'post',
                'guard_name' => 'api',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'put',
                'guard_name' => 'api',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'delete',
                'guard_name' => 'api',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        ]);

        // PERMISSOES DA REGRA
        DB::table('model_has_permissions')->insert([
            [
                'permission_id' => 1,
                'model_type' => 'App\Models\User',
                'model_id' => 1
            ],
            [
                'permission_id' => 2,
                'model_type' => 'App\Models\User',
                'model_id' => 1
            ],
            [
                'permission_id' => 3,
                'model_type' => 'App\Models\User',
                'model_id' => 1
            ],
            [
                'permission_id' => 4,
                'model_type' => 'App\Models\User',
                'model_id' => 1
            ],

        ]);

        // VINCULA REGRA E PERMISSSAO
        DB::table('role_has_permissions')->insert([
            [
                'permission_id' => 1,
                'role_id' => 1
            ],
            [
                'permission_id' => 2,
                'role_id' => 1
            ],
            [
                'permission_id' => 3,
                'role_id' => 1
            ],
            [
                'permission_id' => 4,
                'role_id' => 1
            ],

        ]);

        // REGRA PARA O USUARIO
        DB::table('model_has_roles')->insert([
            [
                'role_id' => 1,
                'model_type' => 'App\Models\User',
                'model_id' => 1
            ],

        ]);
        for ($i = 1; $i <= 200; $i++) {
            $randon = array(1, 2, 3);
            DB::table('model_has_roles')->insert([
                [
                    'role_id' => $randon[array_rand($randon, 1)],
                    'model_type' => 'App\Models\User',
                    'model_id' => $i
                ],

            ]);
        }
    }
}
