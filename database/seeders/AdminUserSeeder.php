<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_user = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt(env('ADMIN_PASSWORD')),
        ]);

        //attach role to created user
        $role = config('roles.models.role')::where('name', '=', 'Admin')->first(); //choose the default role upon user creation.
        $admin_user->attachRole($role);
    }
}
