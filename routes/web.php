<?php

use Illuminate\Support\Facades\Route;
use App\Services\SendNotificationsService;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('test',function(
){
   $fcmToken='eEB95j3hNqlqCEAknAfiF4:APA91bFrKm4XK8J5VHtOQtiaOoCC2qjJ8ilEPGa5F9czG1Ek110AbQKzNuXLu_H4pLK3uDGt8Q8Gdd9A3k6yN5UZ6XMKdxHGCtziprDi_CPW_cYQyCIGKBmR15TV5hNgY5WGce9ImiZC';
    $message=[
        'title'=>'Notification Title',
        'body'=>'Notificattleion Body'
    ];

    (new SendNotificationsService)->sendByFcm($fcmToken,$message);
});
