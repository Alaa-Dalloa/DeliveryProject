<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
      $this->call(UsersTableSeeder::class);
      $this->call(RoleTableSeeder::class);
      $this->call(OwnerResturentSeeder::class);
      $this->call(CategorySeeder::class);
      $this->call(owner_category::class);
      $this->call(MealSeeder::class);
      //$this->call(RoleTableSeeder::class);
      //$this->call(RoleTableSeeder::class);
      //$this->call(RoleTableSeeder::class);
      //$this->call(RoleTableSeeder::class);
      
    }
}
