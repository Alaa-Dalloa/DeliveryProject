<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\category_name;

class owner_category extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       category_name::create([
                'owner_resturent_id' => '1',
                'category_id' => '1',
            ]);
       category_name::create([
                'owner_resturent_id' => '1',
                'category_id' => '2',
            ]);
       category_name::create([
                'owner_resturent_id' => '1',
                'category_id' => '3',
            ]);
       category_name::create([
                'owner_resturent_id' => '1',
                'category_id' => '4',
            ]);
       category_name::create([
                'owner_resturent_id' => '1',
                'category_id' => '5',
            ]);
       category_name::create([
                'owner_resturent_id' => '1',
                'category_id' => '6',
            ]);
       
       category_name::create([
                'owner_resturent_id' => '1',
                'category_id' => '7',
            ]); 

       category_name::create([
                'owner_resturent_id' => '2',
                'category_id' => '8',
            ]); 
       category_name::create([
                'owner_resturent_id' => '2',
                'category_id' => '9',
            ]); 
       category_name::create([
                'owner_resturent_id' => '2',
                'category_id' => '10',
            ]); 
       category_name::create([
                'owner_resturent_id' => '2',
                'category_id' => '11',
            ]); 
       category_name::create([
                'owner_resturent_id' => '2',
                'category_id' => '12',
            ]); 

       category_name::create([
                'owner_resturent_id' => '3',
                'category_id' => '19',
            ]); 

       category_name::create([
                'owner_resturent_id' => '3',
                'category_id' => '18',
            ]); 

        category_name::create([
                'owner_resturent_id' => '4',
                'category_id' => '13',
            ]);

         category_name::create([
                'owner_resturent_id' => '4',
                'category_id' => '14',
            ]);
          category_name::create([
                'owner_resturent_id' => '4',
                'category_id' => '15',
            ]);

          category_name::create([
                'owner_resturent_id' => '4',
                'category_id' => '16',
            ]);

          category_name::create([
                'owner_resturent_id' => '4',
                'category_id' => '20',
            ]);

           category_name::create([
                'owner_resturent_id' => '5',
                'category_id' => '21',
            ]);

            category_name::create([
                'owner_resturent_id' => '5',
                'category_id' => '22',
            ]);
             category_name::create([
                'owner_resturent_id' => '5',
                'category_id' => '23',
            ]);
    }
}