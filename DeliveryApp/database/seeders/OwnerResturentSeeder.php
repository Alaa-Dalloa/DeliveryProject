<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Owner_resturent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\User;

class OwnerResturentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('owner_resturents')->insert([
            [
                'owner_name' => 'John Doe',
                'resturent_name' => 'Thai Cuisine',
                'email' => 'john@gmail.com',
                'password' => bcrypt('1234'),
                'owner_phone' => '123-456-7890',
                'resturent_phone' => '987-654-3210',
            ],
            [
                'owner_name' => 'Jane Smith',
                'resturent_name' => 'Italian Bistro',
                'email' => 'jane@gmail.com',
                'password' => bcrypt('1234'),
                'owner_phone' => '555-555-5555',
                'resturent_phone' => '111-111-1111',
            ],
            [
                'owner_name' => 'Bob Johnson',
                'resturent_name' => 'Burger Shack',
                'email' => 'bob@gmail.com',
                'password' => bcrypt('1234'),
                'owner_phone' => '867-5309',
                'resturent_phone' => '555-2368',
            ],
            [
                'owner_name' => 'Sarah Lee',
                'resturent_name' => 'Sushi Delights',
                'email' => 'sarah@gmail.com',
                'password' => bcrypt('1234'),
                'owner_phone' => '222-222-2222',
                'resturent_phone' => '333-333-3333',
            ],
            [
                'owner_name' => 'Tom Williams',
                'resturent_name' => 'Steakhouse Grill',
                'email' => 'tom@gmail.com',
                'password' => bcrypt('1234'),
                'owner_phone' => '444-444-4444',
                'resturent_phone' => '666-666-6666',
            ],
        ]);

         DB::table('users')->insert([
            [
                'name'=>'John Doe',
                'email'=>'john@gmail.com',
                'password'=>bcrypt('1234'),
                'phone'=>'123-456-7890'
            ],
            [
                'name'=>'Jane Smith',
                'email'=>'jane@gmail.com',
                'password'=>bcrypt('1234'),
                'phone'=>'555-555-5555'
            ],
            [
                'name'=>'Sarah Lee',
                'email'=>'sarah@gmail.com',
                'password'=>bcrypt('1234'),
                'phone'=>'222-222-2222'
            ],
            [
                'name'=>'Tom Williams',
                'email'=>'tom@gmail.com',
                'password'=>bcrypt('1234'),
                'phone'=>'444-444-4444'
            ],
        ]);
        $customerRole = Role::where('name', 'Restaurant_manager')->first();

        // Assign the 'Restaurant_manager' role to each user
        $users = User::all();
        foreach ($users as $user) {
            $user->roles()->attach($customerRole);
        }
    }
}