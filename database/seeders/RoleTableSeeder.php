<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $restaurant_manager=\App\Models\Role::create(
        [
            'name'=>'Restaurant_manager',
            'description'=>'Has permissions to manage meals, offers, advertisements, and transfer orders to the delivery manager',
        ]);

           $delivery_worker=\App\Models\Role::create(
        [
            'name'=>'Delivery_worker',
            'description'=>'Receives delivery orders, delivers them, and confirms the completion of the delivery process',
        ]);
            $customer=\App\Models\Role::create(
        [
            'name'=>'Customer',
            'description'=>'Has permissions to browse offers, meals, advertisements, and place orders',
        ]);

             $Delivery_manger = \App\Models\Role::create([
                'name' => 'Delivery_manger',
                'description' => 'Has permissions to manage delivery orders and assign them for delivery to delivery workers',
            ]);
           

    }
}

