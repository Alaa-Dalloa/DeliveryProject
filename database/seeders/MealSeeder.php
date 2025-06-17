<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Meal;

class MealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Meal::create([
                'name' => 'Cheeseburger',
                'photo' => 'Cheeseburger .jpg',
                'price' => '20',
                'description' => 'يتكون من لحم بقري مشوي، خضروات، صلصة الشيش توك، وقطع الطماطم ',
                'owner_resturent_id' => '1',
                'category_id' => '1'
            ]);

        Meal::create([
                'name' => 'Bacon Cheeseburger',
                'photo' => 'Bacon Cheeseburger.jpg',
                'price' => '25',
                'description' => 'يتميز بإضافة شرائح من لحم البيكون المقرمش وشرائح الجبنة الذائبة',
                'owner_resturent_id' => '1',
                'category_id' => '1'
            ]);

        Meal::create([
                'name' => 'Mushroom Burger',
                'photo' => 'OIP (8).jpg',
                'price' => '30',
                'description' => 'ﻲﻜﻴﺳﻼﻜﻟا ﺮﻏﺮﺒﻟا ﻰﻟﺇ ﻞﺒﺘﻤﻟاﻭ ﻱﻮﺸﻤﻟا ﺮﻄﻔﻟا ﻦﻣ ﺢﺋاﺮﺷ ﺔﻓﺎﺿﺈﺑ ﺰﻴﻤﺘﻳ',
                'owner_resturent_id' => '1',
                'category_id' => '1'
            ]);
        Meal::create([
                'name' => 'Avocado Burger',
                'photo' => 'OIP (9).jpg',
                'price' => '30',
                'description' => 'يتميز بإضافة شرائح من الفطر المشوي والمتبل',
                'owner_resturent_id' => '1',
                'category_id' => '1'
            ]);

        Meal::create([
                'name' => 'Beef Shawarma',
                'photo' => 'OIP (10).jpg',
                'price' => '20',
                'description' => 'يتكون من شرائح لحم مشوي تقدم في خبز الشاورما مع صلصة الثوم والطحينة',
                'owner_resturent_id' => '1',
                'category_id' => '2'
            ]);

         Meal::create([
                'name' => 'Chicken Shawarma',
                'photo' => 'OIP (11).jpg',
                'price' => '25',
                'description' => 'يتكون من شرائح دجاج مشوي تقدم في خبز الشاورما مع صلصة الثوم والطحينة',
                'owner_resturent_id' => '1',
                'category_id' => '2'
            ]);

          Meal::create([
                'name' => 'Fish Shawarma',
                'photo' => 'OIP (12).jpg',
                'price' => '30',
                'description' => 'يتكون من شرائح سمك مشوي تقدم في خبز الشاورما مع صلصة الثوم والطحينة',
                'owner_resturent_id' => '1',
                'category_id' => '2'
            ]);

           Meal::create([
                'name' => 'Spicy Shawarma',
                'photo' => 'OIP (13).jpg',
                'price' => '20',
                'description' => 'يتكون من شرائح دجاج مشوي مع شرائح الفلفل المشوية تقدم في خبز',
                'owner_resturent_id' => '1',
                'category_id' => '2'
            ]);

           Meal::create([
                'name' => 'Crispy Chicken',
                'photo' => 'OIP (14).jpg',
                'price' => '20',
                'description' => 'وجبة تتكون من قطع الدجاج المغمسة في مزيج البانكو',
                'owner_resturent_id' => '1',
                'category_id' => '3'
            ]);

           Meal::create([
                'name' => 'Crispy Fish',
                'photo' => 'OIP (15).jpg',
                'price' => '20',
                'description' => 'وجبة تتكون من قطع السمك المقلية بقشرة خارجية مقرمشة.',
                'owner_resturent_id' => '1',
                'category_id' => '3'
            ]);

           Meal::create([
                'name' => 'Crispy Potato Slices',
                'photo' => 'OIP (16).jpg',
                'price' => '20',
                'description' => 'وجبة تتكون من شرائح البطاطس المقلية',
                'owner_resturent_id' => '1',
                'category_id' => '3'
            ]);

           Meal::create([
                'name' => 'Zinger Chicken',
                'photo' => 'OIP (17).jpg',
                'price' => '20',
                'description' => 'وجبة تتكون من شرائح الدجاج المتبلة والمقرمشة بقشرة خارجية',
                'owner_resturent_id' => '1',
                'category_id' => '4'
            ]);

           Meal::create([
                'name' => 'Zinger Wings',
                'photo' => 'OIP (18).jpg',
                'price' => '20',
                'description' =>'أجنحة الدجاج المقرمشة بقشرة الزنجر والتوابل المميزة.',
                'owner_resturent_id' => '1',
                'category_id' => '4'
            ]);

            Meal::create([
                'name' => 'Zinger Strips',
                'photo' => 'OIP (19).jpg',
                'price' => '20',
                'description' => 'شرائح الدجاج المقرمشة بقشرة الزنجر تقدم على شكل أصابع أو شرائح للتناول كوجبة خفيفة.',
                'owner_resturent_id' => '1',
                'category_id' => '4'
            ]);

           Meal::create([
                'name' => 'Zinger Popcorn',
                'photo' => 'OIP (20).jpg',
                'price' => '20',
                'description' =>'قطع صغيرة من شرائح الدجاج الزنجر المقرمشة بقشرة الزنجر',
                'owner_resturent_id' => '1',
                'category_id' => '4'
            ]);

           Meal::create([
                'name' => 'Chicken Fajita',
                'photo' => 'OIP (21).jpg',
                'price' => '20',
                'description' =>'وجبة تتكون من شرائح الدجاج المشوية مع الفلفل المقطع والبصل والتوابل.',
                'owner_resturent_id' => '1',
                'category_id' => '5'
            ]);

           Meal::create([
                'name' => 'Beef Fajita',
                'photo' => 'OIP (22).jpg',
                'price' => '20',
                'description' =>'وجبة تتكون من شرائح اللحم المشوية مع الفلفل المقطع والبصل والتوابل.',
                'owner_resturent_id' => '1',
                'category_id' => '5'
            ]);

           Meal::create([
                'name' => 'Vegetable Fajita',
                'photo' => 'download (5).jpg',
                'price' => '20',
                'description' =>'وجبة تتكون من مجموعة من الخضروات المشوية مثل الفلفل الملون والبصل والطماطم والفطر والتواب',
                'owner_resturent_id' => '1',
                'category_id' => '5'
            ]);

           Meal::create([
                'name' => 'Cheese Fajita',
                'photo' => 'download (6).jpg',
                'price' => '20',
                'description' =>'وجبة تتكون من شرائح الجبنة المذابة مع الفلفل المقطع والبصل والتوابل.',
                'owner_resturent_id' => '1',
                'category_id' => '5'
            ]);


           Meal::create([
                'name' => 'French Fries',
                'photo' => 'OIP (23).jpg',
                'price' => '20',
                'description' =>'شرائح البطاطس المقلية حتى تصبح ذهبية ومقرمشة',
                'owner_resturent_id' => '1',
                'category_id' => '6'
            ]);

           Meal::create([
                'name' => 'Roasted Potatoes',
                'photo' => 'OIP (24).jpg',
                'price' => '25',
                'description' =>'بطاطس مقطعة إلى قطع صغيرة أو شرائح ومشوية في الفرن مع التوابل والزيوت حتى تصبح مقرمشة من الخارج وناعمة من الداخل.',
                'owner_resturent_id' => '1',
                'category_id' => '6'
            ]);

           Meal::create([
                'name' => 'Cheesy Potato Fries',
                'photo' => 'OIP (25).jpg',
                'price' => '35',
                'description' =>' شرائح البطاطس المقلية المغطاة بالجبنة المذابة وممكن إضافة مكونات إضافية مثل شرائح البيكون أو الفلفل الحار.
',
                'owner_resturent_id' => '1',
                'category_id' => '6'
            ]);

           Meal::create([
                'name' => 'Curry Potatoes',
                'photo' => 'OIP (26).jpg',
                'price' => '30',
                'description' =>' قطع البطاطس المطبوخة في صلصة الكاري المتبلة والمشبعة بالنكهات.',
                'owner_resturent_id' => '1',
                'category_id' => '6'
            ]);


            Meal::create([
                'name' => 'Pepperoni Pizza',
                'photo' => 'OIP (27).jpg',
                'price' => '30',
                'description' =>' بيتزا تتكون من قاعدة عجين مغطاة بصلصة الطماطم وجبنة الموزاريلا وشرائح البيبروني (نوع من السجق المقدد',
                'owner_resturent_id' => '1',
                'category_id' => '7'
            ]);

             Meal::create([
                'name' => 'Margherita Pizza',
                'photo' => 'OIP (28).jpg',
                'price' => '35',
                'description' =>'بيتزا كلاسيكية تتكون من قاعدة عجين مغطاة بصلصة الطماطم وجبنة الموزاريلا وأوراق الريحان.',
                'owner_resturent_id' => '1',
             'category_id' => '7'
            ]);

              Meal::create([
                'name' => 'Hawaiian Pizza',
                'photo' => 'OIP (29).jpg',
                'price' => '30',
                'description' =>'بيتزا تتكون من قاعدة عجين مغطاة بصلصة الطماطم وجبنة الموزاريلا وشرائح الأناناس ولحم الخنزير المقدد.',
                'owner_resturent_id' => '1',
                'category_id' => '7'
            ]);

               Meal::create([
                'name' => 'Vegetable Pizza',
                'photo' => 'OIP (30).jpg',
                'price' => '30',
                'description' =>' بيتزا تتكون من قاعدة عجين مغطاة بصلصة الطماطم وجبنة الموزاريلا ومجموعة متنوعة من الخضروات مثل الفلفل الأخضر والبصل والزيتون والفطر.',
                'owner_resturent_id' => '1',
               'category_id' => '7'
            ]);

                Meal::create([
                'name' => 'Sausage Pizza',
                'photo' => 'OIP (31).jpg',
                'price' => '30',
                'description' =>'بيتزا تتكون من قاعدة عجين مغطاة بصلصة الطماطم وجبنة الموزاريلا وشرائح السجق.',
                'owner_resturent_id' => '1',
               'category_id' => '7'
            ]);


             Meal::create([
                'name' => 'Vanilla',
                'photo' => 'OIP (32).jpg',
                'price' => '5',
                'description' =>'آيس كريم بنكهة الفانيليا الكلاسيكية.',
                'owner_resturent_id' => '2',
               'category_id' => '8'
            ]);

              Meal::create([
                'name' => 'Chocolate',
                'photo' => 'OIP (33).jpg',
                'price' => '5',
                'description' =>'آيس كريم بنكهة الشوكولاتة الغنية والكريمية.',
                'owner_resturent_id' => '2',
               'category_id' => '8'
            ]);

               Meal::create([
                'name' => 'Strawberry',
                'photo' => 'OIP (34).jpg',
                'price' => '5',
                'description' =>'آيس كريم بنكهة الفراولة الطازجة والمنعشة.',
                'owner_resturent_id' => '2',
               'category_id' => '8'
            ]);

                Meal::create([
                'name' => 'Mango',
                'photo' => 'OIP (35).jpg',
                'price' => '5',
                'description' =>'آيس كريم بنكهة المانجو العصيرة والحلوة.',
                'owner_resturent_id' => '2',
               'category_id' => '8'
            ]);


                Meal::create([
                'name' => 'Banana',
                'photo' => 'OIP (36).jpg',
                'price' => '5',
                'description' =>'آيس كريم بنكهة الموز الناعمة والمميزة.',
                'owner_resturent_id' => '2',
               'category_id' => '8'
            ]);


                Meal::create([
                'name' => 'Coffee',
                'photo' => 'OIP (31).jpg',
                'price' => '5',
                'description' =>'آيس كريم بنكهة البسكويت المقرمش والممزوج بالصلصة أو الشوكولاتة.',
                'owner_resturent_id' => '2',
               'category_id' => '8'
            ]);


                Meal::create([
                'name' => 'Lemon',
                'photo' => 'download (7).jpg',
                'price' => '5',
                'description' =>'آيس كريم بنكهة الليمون الحامض والمنعش.',
                'owner_resturent_id' => '2',
               'category_id' => '8'
            ]);


                Meal::create([
                'name' => 'Chocolate Cake',
                'photo' => 'download (8).jpg',
                'price' => '5',
                'description' =>'آيس كريم بنكهة الليمون الحامض والمنعش.',
                'owner_resturent_id' => '2',
               'category_id' => '9'
            ]);

                Meal::create([
                'name' => 'Vanilla Cake',
                'photo' => 'OIP (37).jpg',
                'price' => '5',
                'description' =>'كيك بنكهة الفانيليا اللطيفة والمنعشة.',
                'owner_resturent_id' => '2',
               'category_id' => '9'
            ]);

                  Meal::create([
                'name' => 'Strawberry Cake',
                'photo' => 'OIP (38).jpg',
                'price' => '10',
                'description' =>' كيك بنكهة الفراولة الطازجة والحلوة.',
                'owner_resturent_id' => '2',
               'category_id' => '9'
            ]);

            Meal::create([
                'name' => 'Apple Cake',
                'photo' => 'OIP (39).jpg',
                'price' => '10',
                'description' =>'كيك بنكهة التفاح المقطع والمتبل بالتوابل مثل القرفة والمسكة.',
                'owner_resturent_id' => '2',
               'category_id' => '9'
            ]);


Meal::create([
                'name' => 'Qatayef bil Jebneh',
                'photo' => 'OIP (40).jpg',
                'price' => '20',
                'description' =>'قطايف محشوة بحشوة الجبنة الكريمية والمملحة.',
                'owner_resturent_id' => '2',
               'category_id' => '10'
            ]);

Meal::create([
                'name' => 'Qatayef bil Fustuq',
                'photo' => 'OIP (41).jpg',
                'price' => '10',
                'description' =>' قطايف محشوة بحشوة الفستق المطحون وممكن إضافة السكر والقطر.',
                'owner_resturent_id' => '2',
               'category_id' => '9'
            ]);

Meal::create([
                'name' => 'Qatayef bil Karameel',
                'photo' => 'OIP (42).jpg',
                'price' => '10',
                'description' =>'قطايف محشوة بحشوة الكراميل اللذيذة وممكن إضافة الجوز أو الفستق.',
                'owner_resturent_id' => '2',
               'category_id' => '9'
            ]);


Meal::create([
                'name' => 'Qatayef bil Tamar',
                'photo' => 'OIP (43).jpg',
                'price' => '10',
                'description' =>'قطايف محشوة بحشوة التمر المهروس وممكن إضافة التوابل مثل القرفة والهيل.',
                'owner_resturent_id' => '2',
               'category_id' => '9'
            ]);

Meal::create([
                'name' => 'Mixed Fruit Salad',
                'photo' => 'OIP (44).jpg',
                'price' => '10',
                'description' =>'سلطة تحتوي على مزيج من الفواكه المختلفة مثل العنب والتفاح والبرتقال والمانجو.',
                'owner_resturent_id' => '2',
               'category_id' => '10'
            ]);

Meal::create([
                'name' => 'Fruit Salad with Mint',
                'photo' => 'OIP (45).jpg',
                'price' => '10',
                'description' =>'سلطة تحتوي على فواكه مقطعة مع إضافة نعناع طازج لإعطاء نكهة منعشة.',
                'owner_resturent_id' => '2',
               'category_id' => '10'
            ]);

Meal::create([
                'name' => 'Fruit Salad with Honey',
                'photo' => 'OIP (46).jpg',
                'price' => '10',
                'description' =>'سلطة تحتوي على فواكه مقطعة مع إضافة قليل من العسل لإضفاء حلاوة طبيعية.',
                'owner_resturent_id' => '2',
               'category_id' => '10'
            ]);

Meal::create([
                'name' => 'Frozen Fruit Salad',
                'photo' => 'OIP (47).jpg',
                'price' => '10',
                'description' =>' سلطة تحتوي على فواكه مجمدة مثل الفراولة والتوت والمانجو والعنب المجمد.',
                'owner_resturent_id' => '2',
               'category_id' => '10'
            ]);


Meal::create([
                'name' => 'Tropical Fruit Salad',
                'photo' => 'download (9).jpg',
                'price' => '10',
                'description' =>'سلطة تحتوي على فواكه استوائية مثل الأناناس والمانجو والبابايا والكيوي.',
                'owner_resturent_id' => '2',
               'category_id' => '10'
            ]);

Meal::create([
                'name' => 'Orange Juice',
                'photo' => 'OIP (48).jpg',
                'price' => '10',
                'description' =>'عصير مصنوع من عصير البرتقال الطازج.',
                'owner_resturent_id' => '2',
               'category_id' => '11'
            ]);

Meal::create([
                'name' => 'Apple Juice',
                'photo' => 'OIP (49).jpg',
                'price' => '10',
                'description' =>'عصير مصنوع من عصير التفاح الطازج.',
                'owner_resturent_id' => '2',
               'category_id' => '11'
            ]);

Meal::create([
                'name' => 'Lemonade',
                'photo' => 'OIP (50).jpg',
                'price' => '10',
                'description' =>'مشروب مصنوع من عصير الليمون الممزوج بالماء والسكر.',
                'owner_resturent_id' => '2',
               'category_id' => '11'
            ]);

Meal::create([
                'name' => 'Guava Juice',
                'photo' => 'OIP (51).jpg',
                'price' => '10',
                'description' =>'عصير مصنوع من عصير الجوافة الناعم والمنعش',
                'owner_resturent_id' => '2',
               'category_id' => '11'
            ]);

Meal::create([
                'name' => 'Pineapple Juice',
                'photo' => 'download (10).jpg',
                'price' => '10',
                'description' =>'عصير مصنوع من عصير الأناناس الطازج أو المعلب.',
                'owner_resturent_id' => '2',
               'category_id' => '11'
            ]);

Meal::create([
                'name' => 'Iced Coffee',
                'photo' => 'OIP (52).jpg',
                'price' => '10',
                'description' =>'مشروب قهوة مثلجة يحتوي على القهوة المبردة والحليب والسكر أو الشراب.',
                'owner_resturent_id' => '2',
               'category_id' => '11'
            ]);

Meal::create([
                'name' => 'Grape Juice',
                'photo' => 'OIP (53).jpg',
                'price' => '10',
                'description' =>'عصير مصنوع من عصير العنب الطازج أو المعصور.',
                'owner_resturent_id' => '2',
               'category_id' => '11'
            ]);

         }

}
