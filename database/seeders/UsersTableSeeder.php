<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
      public function run()
    {
        $user = User::create([
            'name' => 'Msallam',
            'email' => 'msallam@gmail.com',
            'password' => Hash::make('secret1234'),
            'phone' => 'null',
        ]);

        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $user->roles()->attach($superAdminRole);
        } else {
            $superAdminRole = Role::create([
                'name' => 'super_admin',
                'description' => 'Has full permissions',
            ]);
            $user->roles()->attach($superAdminRole);
        }


    }
}
