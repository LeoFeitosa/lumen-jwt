<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 200; $i++) {
            factory(\App\Models\User::class, 1)->create([
                'email' => 'user@user.com',
                'password' => 'secret'
            ]);
        }
    }
}
