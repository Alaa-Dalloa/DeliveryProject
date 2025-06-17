<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
                'name' => 'Berger',
                'photo' => '5BURGER.png',
            ]);

         Category::create([
                'name' => 'shawarma',
                'photo' => 'OIP.jpg',
            ]);

          Category::create([
                'name' => 'Crispy',
                'photo' => 'crysbi.png',
            ]);
           Category::create([
                'name' => 'Zinger',
                'photo' => 'zenger.png',
            ]);
            Category::create([
                'name' => 'Fajita',
                'photo' => 'OIP(1).jpg',
            ]);

            
             Category::create([
                'name' => 'Potato',
                'photo' => 'OIP4.jpg',
            ]);

             Category::create([
                'name' => 'Pizza',
                'photo' => 'OIP3.jpg',
            ]);

              Category::create([
                'name' => 'Ice Cream',
                'photo' => 'download (2).jpg',
            ]);

        
              Category::create([
                'name' => 'Cake',
                'photo' => 'OIP44.jpg',
            ]); 

              Category::create([
                'name' => 'Qatayef',
                'photo' => 'OIP5.jpg',
            ]);

              Category::create([
                'name' => 'Fruit Salad',
                'photo' =>'OIP6.jpg',
            ]);


              Category::create([
                'name' => 'Cold Beverage',
                'photo' => 'OIP7.jpg',
            ]);


              Category::create([
                'name' => 'Biryani',
                'photo' => 'OIP 99.jpg',
            ]);

              Category::create([
                'name' => 'Kebab',
                'photo' => 'OIP (2).jpg',
            ]);
              Category::create([
                'name' => 'Shaqaf',
                'photo' => 'OIP (3).jpg']);
           
            Category::create([
                'name' => 'Grilled Chicken',
                'photo' => 'OIP (4).jpg',
            ]);

            Category::create([
                'name' => 'Charcoal-Grilled Shawarma',
                'photo' => 'OIP (5).jpg',
            ]);


            Category::create([
                'name' => 'fried fish',
                'photo' => 'download (3).jpg',
            ]);

            Category::create([
                'name' => 'Grilled fish',
                'photo' => 'OIP (6).jpg',
            ]);

            Category::create([
                'name' => 'kofta',
                'photo' => 'OIP (7).jpg',
            ]);


            Category::create([
                'name' => 'Falafel',
                'photo' => 'OIP55.jpg',
            ]);

            Category::create([
                'name' => 'Foul',
                'photo' => 'th.jpg',
            ]);


            Category::create([
                'name' => 'Fattah',
                'photo' => 'download (4).jpg',
            ]);
           

}

}
