<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OwnerResturentController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OfferMetaMeal;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\BearenController;
use App\Http\Controllers\StarController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddOnsController;
use App\Http\Controllers\WithoutController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ComplaintController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([

    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'auth'

], function ($router) {

    //Auth

    Route::post('login', 'AuthController@login');

    Route::post('logout', 'AuthController@logout');

    Route::get('profile', 'AuthController@profile');

    Route::post('register', 'AuthController@register');
    
    Route::get('getCustomers', 'AuthController@getCustomers');

    Route::get('getUserInfo/{id}','AuthController@getUserInfo');


   Route::post('saveFcmToken', 'AuthController@saveFcmToken');

    //Address

    Route::post('addAddress', 'AuthController@addAddress');

    Route::post('updateAddress/{id}', 'AuthController@updateAddress');

    Route::post('deleteAddress/{id}', 'AuthController@deleteAddress');

    Route::get('getAddress/{id}', 'AuthController@getAddress');

    Route::get('getUserAddresses', 'AuthController@getUserAddresses');

     Route::get('DetaileAddress/{id}', 'AuthController@DetaileAddress');

     Route::get('getDeliveryAddresses/{id}', 'AuthController@getDeliveryAddresses')->middleware('Delivery_manger');
    
    //Resturent

    Route::post('AddAccountResturent', 'OwnerResturentController@AddAccountResturent')->middleware('Super_admin');

    Route::get('getOwner_resturent', 'OwnerResturentController@getOwner_resturent');

   Route::get('SearchOfResturent/{resturent_name}', 'OwnerResturentController@SearchOfResturent');
   Route::post('updateRasturent/{id}', 'OwnerResturentController@updateRasturent')->middleware('Restaurant_manager');
    Route::get('showDetailResturent/{id}', 'OwnerResturentController@showDetailResturent');

  
  Route::get('GetIdResturentByToken', 'OwnerResturentController@GetIdResturentByToken');

   
   //Delivery

   Route::post('AddAccountDeliveryManger', 'DeliveryController@AddAccountDeliveryManger')->middleware('Super_admin');

   Route::post('AddAccountDelivery', 'DeliveryController@AddAccountDelivery')->middleware('Delivery_manger');

   Route::get('getActiveDeliveryWorkers', 'DeliveryController@getActiveDeliveryWorkers')->middleware('Delivery_manger');

   Route::post('Set_not_available/{id}', 'DeliveryController@Set_not_available')->middleware('Delivery_manger');
   
  Route::post('Set_available/{id}', 'DeliveryController@Set_available')->middleware('Delivery_manger');

  Route::post('updateDelivery/{id}', 'DeliveryController@updateDelivery')->middleware('Delivery_manger');

  Route::post('Cancel_delivery_worker/{id}', 'DeliveryController@cancelDeliveryWorker')->middleware('Delivery_manger');

  Route::post('updateStatusDelivery_received/{id}', 'DeliveryController@updateStatusDelivery_received')->middleware('Delivery_worker');

   Route::post('AssignOrderToDelivery/{OrderId}/{deliveryId}', 'DeliveryController@assignOrderToDelivery')->middleware('Delivery_manger');


  //Category
 
    Route::post('createCategory', 'CategoryController@create')->middleware('Restaurant_manager');
    Route::post('update/{id}', 'CategoryController@update')->middleware('Restaurant_manager');

    Route::get('allCategory', 'CategoryController@index');

    Route::post('delete/{id}', 'CategoryController@delete')->middleware('Restaurant_manager');

    Route::get('showDetail/{id}', 'CategoryController@show');

    Route::get('/{id}/searchOfCategory/{name}', 'CategoryController@searchOfCategory');

    Route::get('getCategoryMyOwner', 'CategoryController@getCategoryMyOwner')->middleware('Restaurant_manager');

    Route::get('getCategoryByOwner/{id}', 'CategoryController@getCategoryByOwner');


  //Meal

    Route::post('addMeal', 'MealController@addMeal')->middleware('Restaurant_manager');

    Route::get('MealByResturent', 'MealController@MealByResturent')->middleware('Restaurant_manager');

    Route::get('MealByResturentById/{id}', 'MealController@MealByResturentById')->middleware('Restaurant_manager');

    Route::get('/{resturentId}/getMealsByCategoryAndOwner/{categoryId}', 'MealController@getMealsByCategoryAndOwner');

    Route::get('showDetailMeal/{id}', 'MealController@showDetailMeal');

    Route::post('updateMeal/{id}', 'MealController@updateMeal')->middleware('Restaurant_manager');

    Route::post('deleteMeal/{id}', 'MealController@delete')->middleware('Restaurant_manager');
    
    Route::post('addRatingToMeal/{id}', 'StarController@addRatingToMeal');

    Route::get('getLastRatingForMeal/{id}', 'StarController@getLastRatingForMeal');

    Route::post('addToFavorites/{id}', 'FavoriteController@addToFavorites');

    Route::get('getFavorites', 'FavoriteController@getFavorites');
    
   Route::post('removeFromFavorites/{id}', 'FavoriteController@removeFromFavorites');


    Route::get('searchOfMeal/{restaurant_id}/{category_id}/{mealName}', 'MealController@searchOfMeal');

    //offer
    Route::post('addOfferToMeal/{id}', 'OfferController@addOfferToMeal')->middleware('Restaurant_manager');

    Route::get('getOffers/{id}', 'OfferController@getOffers');

    Route::post('deleteOffer/{id}', 'OfferController@deleteOffer')->middleware('Restaurant_manager');

    Route::post('editOffer/{id}', 'OfferController@editOffer')->middleware('Restaurant_manager');


 
   //OfferToMeals

  Route::post('addOfferToMeals', 'OfferMetaMeal@addOfferToMeals')->middleware('Restaurant_manager');

  Route::get('allOfferPackage/{id}', 'OfferMetaMeal@getPackageOffers');

  Route::get('detailOfferPackage/{id}', 'OfferMetaMeal@detailOfferPackage');

  Route::post('deletePackage/{id}', 'OfferMetaMeal@deletePackage')->middleware('Restaurant_manager');

  Route::post('editOfferPackage/{id}', 'OfferMetaMeal@editOfferPackage')->middleware('Restaurant_manager');


//Advertisement

  Route::post('addAdvertisement/{itemId}', 'AdvertisementController@addAdvertisement')->middleware('Restaurant_manager');

  Route::get('showAdvertisements', 'AdvertisementController@showAdvertisements');

  Route::post('AgreeAdvertisement/{id}', 'AdvertisementController@AgreeAdvertisement')->middleware('Super_admin');

  Route::post('Decline_to_advertise/{id}', 'AdvertisementController@Decline_to_advertise')->middleware('Super_admin');


 Route::get('allAdvertisements', 'AdvertisementController@allAdvertisements')->middleware('Super_admin');

//Order

  Route::post('addOrder', 'OrderController@addOrder');

  Route::get('getOrderDetails/{id}', 'OrderController@getOrderDetails');

  Route::get('showAllOrdersReadyForDelivery', 'OrderController@showAllOrdersReady')->middleware('Delivery_worker');

  Route::get('showAllOrdersUnder_DeliveryForDelivery', 'OrderController@showAllOrdersUnderDelivery')->middleware('Delivery_worker');


   Route::get('GetMyCurrentOrder', 'OrderController@GetMyCurrentOrder');

   Route::get('GetMyOrderArchive', 'OrderController@GetMyOrderArchive');

   Route::get('GetOrderForResturent/{id}', 'OrderController@GetOrderForResturent')->middleware('Restaurant_manager');

   Route::get('AllOrderForMangerDelivery', 'OrderController@AllOrderForMangerDelivery')->middleware('Delivery_manger');

   Route::get('AllOrder_UnderDelivery_ForMangerDelivery', 'OrderController@AllOrder_UnderDelivery_ForMangerDelivery')->middleware('Delivery_manger');

 Route::get('AllOrder_Ready_ForMangerDelivery', 'OrderController@AllOrder_Ready_ForMangerDelivery')->middleware('Delivery_manger');


  Route::post('cancelOrder/{id}', 'OrderController@cancelOrder');

  Route::post('reorder/{id}', 'OrderController@reorder');

  Route::get('getMostOrderedMeals', 'OrderController@getMostOrderedMeals');

  Route::post('updateOrderStatusToBeingProcessed/{id}', 'OrderController@updateOrderStatusToBeingProcessed')->middleware('Restaurant_manager');

  Route::post('updateOrderStatusToReady/{id}', 'OrderController@updateOrderStatusToReady')->middleware('Restaurant_manager');


  Route::post('confirmDeliveryOrderByResturent/{id}', 'OrderController@confirmDeliveryOrderByResturent')->middleware('Restaurant_manager');

Route::post('updateOrderStatusToDelivered/{id}', 'OrderController@updateOrderStatusToDelivered')->middleware('Delivery_worker');


Route::post('calculateDistance', 'OrderController@calculateDistance');

//comment

  
  Route::post('addCommentToMeal/{id}', 'CommentController@addCommentToMeal');
  
  Route::post('updateComment/{id}', 'CommentController@updateComment');

  Route::post('deleteComment/{id}', 'CommentController@deleteComment');

  Route::get('showMealComments/{id}', 'CommentController@showMealComments');

//Bearen


Route::get('getAllBearenDaily', 'BearenController@getAllBearen')->middleware('Super_admin');
Route::get('getAllBearenMonthly', 'BearenController@getAllBearenMonthly')->middleware('Super_admin');
Route::get('getAllBearenYearly', 'BearenController@getAllBearenYearly')->middleware('Super_admin');


Route::get('getBearenDaily/{id}', 'BearenController@getBearen')->middleware('Restaurant_manager');
Route::get('getMonthlyInventory/{id}', 'BearenController@getMonthlyInventory')->middleware('Restaurant_manager');
Route::get('getYearlyInventory/{id}', 'BearenController@getYearlyInventory')->middleware('Restaurant_manager');



Route::get('getRestaurantOrders/{id}', 'BearenController@getRestaurantOrders');

Route::get('getTotalSalesByWeekSuperAdmin/{id}', 'BearenController@getTotalSalesByWeekSuperAdmin')->middleware('Super_admin');
Route::get('getTotalSalesByYearForSuperAdmin/{id}', 'BearenController@getTotalSalesByYearForSuperAdmin')->middleware('Super_admin');
Route::get('GetTotalSaleByMounthForSuperAdmin/{id}', 'BearenController@GetTotalSaleByMounthForSuperAdmin')->middleware('Super_admin');


Route::get('getTotalSalesByWeek/{id}', 'BearenController@getTotalSalesByWeek')->middleware('Restaurant_manager');

Route::get('getTotalSalesByYear/{id}', 'BearenController@getTotalSalesByYear')->middleware('Restaurant_manager');

Route::get('getTotalSaleByMounth/{id}', 'BearenController@getTotalSaleByMounth')->middleware('Restaurant_manager');




//account

 Route::post('addBankCard', 'AccountController@addBankCard');

Route::post('makePayment/{id}', 'AccountController@makePayment');

Route::post('makePaymentByPoints/{id}', 'AccountController@makePaymentByPoints');


Route::post('makePaymentByElecBank/{id}', 'AccountController@makePaymentByElecBank');


//Addion$getWithoutDetails


Route::get('getAddonsDetails/{id}', 'AddOnsController@getAddonsDetails');

Route::get('showDetailAddionMeal/{id}', 'AddOnsController@showDetailAddionMeal');

Route::get('getWithoutDetails/{id}', 'WithoutController@getWithoutDetails');

Route::get('showDetailWithoutMeal/{id}', 'WithoutController@showDetailWithoutMeal');


Route::post('addNewAddonsToMeal/{id}', 'MealController@addNewAddonsToMeal')->middleware('Restaurant_manager');

 Route::post('addNewWitoutToMeal/{id}', 'MealController@addNewWitoutToMeal')->middleware('Restaurant_manager');


 //Complaint

 Route::post('addComplaint/{id}', 'ComplaintController@addComplaint');

Route::get('allComplaint', 'ComplaintController@allComplaint')->middleware('Delivery_manger');

Route::get('DetailComplaint/{id}', 'ComplaintController@DetailComplaint')->middleware('Delivery_manger');

Route::get('getDeliveryWorker', 'AuthController@getDeliveryWorker')->middleware('Delivery_manger');

Route::get('getComplaintByWorker/{id}', 'ComplaintController@getComplaintByWorker')->middleware('Delivery_manger');



});


