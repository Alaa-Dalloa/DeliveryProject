<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Meal;
use App\Models\User;
use App\Models\Order;
use App\Models\Offer;
use App\Models\Account;
use App\Models\Address;
use App\Models\BearenM;
use App\Models\Owner_resturent;
use App\Models\Add_ons;
use App\Models\Without;
use App\Models\Bearen;
use App\Models\BearenYear;
use Illuminate\Support\Facades\Validator;
use App\Services\SendNotificationsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use DB;

class OrderController extends Controller
{

public function makePayment(Request $request, $orderId)
{
    $id = Auth::user()->id;
    $password = $request->input('password');
    $card_number = $request->input('card_number');

    $account = Account::where('user_id', $id)
        ->where('password', $password)
        ->where('card_number', $card_number)
        ->first();

    if (!$account) {
        return response()->json(['message' => 'Invalid password or card number. Please check your credentials.']);
    }

    $order = Order::find($orderId);
    $orderPrice = $order->order_price + $order->delivery_cost;

    if ($order->paid == 1) {
        return response()->json(['message' => 'The order is already paid.']);
    }

    if ($account->account < $orderPrice) {
        return response()->json(['message' => 'You do not have enough balance to complete the payment']);
    }

    $order->paid = 1;
    $order->save();

    $account->account-= $orderPrice;
    $account->save();

    return response()->json(['message' => 'Payment was made and the amount was debited successfully']);
}


public function addAddress(Request $request, $userId)
{
    $user = User::find($userId);
    if (!$user) 
    {
        return response()->json(['error' => 'User not found'], 404);
    }

    $address = new Address;
    $address->y = $request->input('y');
   $address->x = $request->input('x');
   $address->address_name = $request->input('address_name');
    $address->save();

    $user->addresses()->attach($address);

    return $address;
}



private function getOrCreateAddress(Request $request, $userId)
{
    if ($request->has('address_id')) {
        $addressId = $request->input('address_id');
        $address = Address::find($addressId);
        if ($address) {
            if ($address->users()->where('user_id', $userId)->exists())
             {
                return $address;
            }
        }
    }

    $address = $this->addAddress($request, $userId);
    $user = User::find($userId);
    if (!$user->addresses()->where('address_id', $address->id)->exists()) {
        $user->addresses()->attach($address);
    }

        return $address;
}


public function addOrder(Request $request)
{
    $user = auth()->user();
    if ($user->blocked_until && now()->tz('Europe/London')->addHours(2)->lt($user->blocked_until)) {
        return response()->json('You are currently blocked from requesting orders until ' . $user->blocked_until, 400);
    }

    $status = 'Recipient';
    $order_date = Carbon::now()->tz('Europe/London')->addHours(2);
    $delivery_cost = $request->input('delivery_cost');
    $user_id = $user->id;
    $order_price = 0;

    $address = $this->getOrCreateAddress($request, $user->id);

    // Create the order
    $order = Order::create([
        'delivery_cost' => $delivery_cost,
        'order_price' => $order_price,
        'order_date' => $order_date,
        'user_id' => $user_id,
        'address_id' => $address->id
    ]);
    
    

    // Attach meals to the order and calculate order price
    if ($request->has('meal_ids')) {
        foreach ($request->input('meal_ids') as $mealData) {
            $mealId = $mealData['mealId'];
            $quantity = $mealData['quantity'];
            $size = $mealData['size']?? 'Medium'; // Get the meal size
            $addons = $mealData['addons'] ?? [];
            $withouts = $mealData['withouts'] ?? [];

            $meal = Meal::find($mealId);
            if ($meal) {
                $order->meals()->attach($meal, [
                    'quantity' => $quantity,
                    'size' => $size, // Store the meal size
                    'addons' => json_encode($addons),
                    'withouts' => json_encode($withouts)
                ]);

                $mealPrice = $meal->price_after_discount ?? $meal->price;

                // Adjust the meal price based on the size
                if ($size == 'small') {
                    // No additional charge
                } elseif ($size == 'Medium') {
                    $mealPrice += $mealPrice/2;
                } elseif ($size == 'large') {
                    $mealPrice += $mealPrice*2; 
                }
                $order_price += $mealPrice * $quantity;
            }
        }
    }

    // Attach offers to the order and calculate order price
    if ($request->has('offer_ids')) {
        foreach ($request->input('offer_ids') as $offerData) {
            $offerId = $offerData['offerId'];
            $quantity = $offerData['quantity'];

            $offer = Offer::find($offerId);
            if ($offer) {
                $order->offers()->attach($offer, ['quantity' => $quantity]);
                $offerPrice = Offer::where('id', $offerId)->value('price_after_discount');
                $order_price += $offerPrice * $quantity;
            }
        }
    }

    // Update the order price
    $order->update(['order_price' => $order_price]);

    // Make payment
    // $this->makePayment($request, $order->id);

    // Attach the owner_resturent to the order
    $mealIds = collect($request->input('meal_ids'))->pluck('mealId')->toArray();
    $owner_resturent_ids = Meal::whereIn('id', $mealIds)->pluck('owner_resturent_id')->unique()->toArray();
    $owner_resturents = Owner_resturent::whereIn('id', $owner_resturent_ids)->get();
    $order->owner_resturents()->attach($owner_resturents);


// Update daily sales for each owner_resturent
foreach ($owner_resturents as $owner_resturent) {
    // Check if there is an existing record for today's date
    $existingRecord = Bearen::where('owner_resturent_id', $owner_resturent->id)->where('daily_sale_date',Carbon::now()->tz('Europe/London')->addHours(2)->format('m-d'))
    ->first();

    if ($existingRecord) {
        // Update the existing record
        $existingRecord->total_sales += $order->order_price;
        $existingRecord->total_delivery_cost += $order->delivery_cost;
        $existingRecord->total_summation = $existingRecord->total_sales + $existingRecord->total_delivery_cost;
        $existingRecord->save();
    } else {
        // Create a new record
        $bearen = new Bearen;
        $bearen->daily_sale_date = Carbon::now()->tz('Europe/London')->addHours(2)->format('m-d');
        $bearen->total_sales = $order->order_price;
        $bearen->total_delivery_cost = $order->delivery_cost;
        $bearen->total_summation = $order->order_price + $order->delivery_cost;
        $bearen->owner_resturent_id = $owner_resturent->id;
        $bearen->save();
    }
}

// Update mounthly sales for each owner_resturent
foreach ($owner_resturents as $owner_resturent) {
    // Check if there is an existing record for today's date
    $existingRecord = BearenM::where('owner_resturent_id', $owner_resturent->id)->where('monthly_sale_date', Carbon::now()->tz('Europe/London')->addHours(2)->format('m'))->first();

    if ($existingRecord) {
        // Update the existing record
        $existingRecord->total_sales += $order->order_price;
        $existingRecord->total_delivery_cost += $order->delivery_cost;
        $existingRecord->total_summation = $existingRecord->total_sales + $existingRecord->total_delivery_cost;
        $existingRecord->save();
    } else {
        // Create a new record
        $bearen = new BearenM;
        $bearen->monthly_sale_date = Carbon::now()->tz('Europe/London')->addHours(2)->format('m');
        $bearen->total_sales = $order->order_price;
        $bearen->total_delivery_cost = $order->delivery_cost;
        $bearen->total_summation = $order->order_price + $order->delivery_cost;
        $bearen->owner_resturent_id = $owner_resturent->id;
        $bearen->save();
    }
}

// Update yearly sales for each owner_resturent
foreach ($owner_resturents as $owner_resturent) {
    // Check if there is an existing record for today's date
    $existingRecord = BearenYear::where('owner_resturent_id', $owner_resturent->id)->where('annual_sale_date', Carbon::now()->tz('Europe/London')->addHours(2)->format('Y'))
                         ->first();

    if ($existingRecord) {
        // Update the existing record
        $existingRecord->total_sales += $order->order_price;
        $existingRecord->total_delivery_cost += $order->delivery_cost;
        $existingRecord->total_summation = $existingRecord->total_sales + $existingRecord->total_delivery_cost;
        $existingRecord->save();
    } else {
        // Create a new record
        $bearen = new BearenYear;
        $bearen->annual_sale_date = Carbon::now()->tz('Europe/London')->addHours(2)->format('Y');
        $bearen->total_sales = $order->order_price;
        $bearen->total_delivery_cost = $order->delivery_cost;
        $bearen->total_summation = $order->order_price + $order->delivery_cost;
        $bearen->owner_resturent_id = $owner_resturent->id;
        $bearen->save();
    }
}
    
    return response()->json("Order added successfully", 201);
}

public function getOrderDetails($orderId)
{
    $order = Order::with(['offers', 'user', 'address'])->find($orderId);
    $delivery_worker_id=Order::where('id',$orderId)->value('delivery_worker_id');
    $delivery_worker=User::where('id',$delivery_worker_id)->get();


    $mealsWithAddonsAndWithouts = [];
    foreach ($order->meals as $meal) {
        $mealData = $meal->toArray();

        // Extract information about addons and withouts from the "addons" and "withouts" fields
        $addons = json_decode($meal->pivot->addons, true) ?? [];
        $withouts = json_decode($meal->pivot->withouts, true) ?? [];

        // Fetch the addon names based on the addon IDs
                $addonNames = [];
        foreach ($addons as $addon) {
            $addonModel = Add_ons::find($addon['addonId']);
            if ($addonModel) {
                $addonNames[] = [
                    'addonId' => $addon['addonId'],
                    'addon_name' => $addonModel->addon
                ];
            }
        }

        $withoutNames = [];
        foreach ($withouts as $without) {
            $withoutModel = Without::find($without['withoutId']);
            if ($withoutModel) {
                $withoutNames[] = [
                    'withoutId' => $without['withoutId'],
                    'without_name' => $withoutModel->without_name
                ];
            }
}

        $mealData['quantity'] = $meal->pivot->quantity;
        $mealData['size'] = $meal->pivot->size;
        $mealData['addons'] = $addonNames;
        $mealData['withouts'] = $withoutNames;
        unset($mealData['pivot']);

        $mealsWithAddonsAndWithouts[] = $mealData;
    }
    $orderr = Order::where('id',$orderId)->select('id','status','order_price','delivery_cost','order_date')->first();

    $restaurantName = $order->owner_resturents->first()->resturent_name;

    return response()->json([
        'order' => $orderr,
        'offers' => $order->offers,
        'user' => $order->user,
        'address' => $order->address,
        'meals' => $mealsWithAddonsAndWithouts,
        'restaurant_name' => $restaurantName,
        'delivery_worker'=>$delivery_worker
    ]);
}

public function showAllOrdersReady()
{
    $user = auth()->user();
    $orders = Order::whereIn('status', ['Ready'])
     ->where('delivery_worker_id', $user->id)
      ->with(['user', 'address', 'owner_resturents'])
       ->get();

    $ordersWithDetails = $orders->map(function ($order) {
    $restaurantDetails = $order->owner_resturents->map(function ($resturent) {
   $user_id = User::where('email', $resturent->email)->value('id');
    $restaurant_address = DB::table('address_user')
     ->join('addresses', 'address_user.address_id', '=', 'addresses.id')
     ->where('address_user.user_id', $user_id)
    ->select('addresses.address_name', 'addresses.x', 'addresses.y')
 ->first();

return [
    'restaurant_name' => $resturent->resturent_name,
    'restaurant_address' => $restaurant_address ? [
        'address_name' => $restaurant_address->address_name,
        'x' => $restaurant_address->x,
        'y' => $restaurant_address->y
    ] : null,
];
        });

        return [
            'id' => $order->id,
            'status' => $order->status,
            'order_price' => $order->order_price,
            'delivery_cost' => $order->delivery_cost,
            'order_date' => $order->order_date,
            'paid' => $order->paid,
            'delivery_received' => $order->delivery_received,
            'delivery_worker_id' => $order->delivery_worker_id,
            'customer_name' => $order->user->name,
            'customer_phone' => $order->user->phone,
            'restaurant_details' => $restaurantDetails,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at
        ];
    });

    return response()->json($ordersWithDetails);
}


public function showAllOrdersUnderDelivery()
{
    $user = auth()->user();
    $orders = Order::whereIn('status', ['Under_delivery'])->where('delivery_worker_id', $user->id)
      ->with(['user', 'address', 'owner_resturents'])
       ->get();


    $ordersWithDetails = $orders->map(function ($order) {
    $restaurantDetails = $order->owner_resturents->map(function ($resturent) {
   $user_id = User::where('email', $resturent->email)->value('id');
    $restaurant_address = DB::table('address_user')
     ->join('addresses', 'address_user.address_id', '=', 'addresses.id')
     ->where('address_user.user_id', $user_id)
    ->select('addresses.address_name', 'addresses.x', 'addresses.y')
 ->first();

return [
    'restaurant_name' => $resturent->resturent_name,
    'restaurant_address' => $restaurant_address ? [
        'address_name' => $restaurant_address->address_name,
        'x' => $restaurant_address->x,
        'y' => $restaurant_address->y
    ] : null,
];
        });

        return [
            'id' => $order->id,
            'status' => $order->status,
            'order_price' => $order->order_price,
            'delivery_cost' => $order->delivery_cost,
            'order_date' => $order->order_date,
            'paid' => $order->paid,
            'delivery_received' => $order->delivery_received,
            'delivery_worker_id' => $order->delivery_worker_id,
            'customer_name' => $order->user->name,
            'customer_phone' => $order->user->phone,
            'restaurant_details' => $restaurantDetails,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at
        ];
    });

    return response()->json($ordersWithDetails);
}


public function GetMyCurrentOrder()
{
    $user = auth()->user();
    $orders = Order::where('status', '!=', 'Delivered')
    ->where('user_id', $user->id)
    ->get();

    return response()->json($orders);
}

public function GetOrderForResturent($restaurantId)
{
    $orders = Order::whereHas('owner_resturents', function ($query) use ($restaurantId) {
        $query->where('owner_resturent_id', $restaurantId);
    })
    ->whereNotIn('status', ['Under_delivery', 'Delivered']) // Exclude orders with these statuses
    ->where('paid', 1) // Add condition for paid orders
    ->get();

    return response()->json($orders);
}

public function AllOrderForMangerDelivery()
{
    $orders = Order::where('status', 'Ready')->where('delivery_worker_id',"=","null")
                   ->get();

    return response()->json($orders);
}

public function AllOrder_UnderDelivery_ForMangerDelivery()
{
 $orders = Order::where('status', 'Under_delivery')->where('delivery_worker_id',"!=","null")
                    ->get();

    return response()->json($orders);
}

public function AllOrder_Ready_ForMangerDelivery()
{
 $orders = Order::where('status', 'Ready')->where('delivery_worker_id',"!=","null")
                    ->get();

    return response()->json($orders);
}

public function GetMyOrderArchive()
{
    $user = auth()->user();
    $orders = Order::where('status', 'Delivered')
                    ->where('user_id', $user->id)
                    ->get();

    return response()->json($orders);
}


public function cancelOrder($orderId)
{
    $order = Order::find($orderId);

    if ($order) { // Check if the order exists
        if ($order->status === 'Recipient') {
            $totalAmount = $order->order_price + $order->delivery_cost;

            $userId = Auth::user()->id;

            $order->user()->dissociate();
            $order->meals()->detach();
            $order->owner_resturents()->detach();

            $order->delete();

            $user = User::find($userId);
            $user->cancel_count += 1;

            // Calculate blocked_until based on previous block
            if ($user->cancel_count >= 6) {
                $user->blocked_until = now()->tz('Europe/London')->addDays(7); // Block for 7 days
            } elseif ($user->cancel_count >= 4) {
                $user->blocked_until = now()->tz('Europe/London')->addDays(3); // Block for 3 days
            } elseif ($user->cancel_count >= 2) {
                $user->blocked_until = now()->tz('Europe/London')->addDays(1); // Block for 1 day
            } else {
                $user->blocked_until = null; // No block for 1 or 2 cancellations
            }
                $order_price=$order->order_price;
                $pointt =$order_price/ 10;
                $user->points +=$pointt;
                $user->save();

            $account = Account::where('user_id', $userId)->first();

            if ($account) {
                $account->account += $totalAmount;
                $account->save();
            }

            return response()->json('Order cancelled successfully. Amount added to your account: ' . $totalAmount);
        } else {
            return response()->json('Unable to cancel the order because the order is ' . $order->status, 400);
        }
    } else {
        return response()->json('Order not found', 404); // Return 404 if order not found
    }
}
public function reorder(Request $request, $orderId)
{
    $user = auth()->user();

    if ($user->blocked_until && now()->tz('Europe/London')->addHours(2)->lt($user->blocked_until)) {
        return response()->json('You are currently blocked from requesting orders until ' . $user->blocked_until, 400);
    }

    $order = Order::findOrFail($orderId);

    if ($user->id !== $order->user_id) {
        return response()->json('You are not authorized to reorder this order.', 403);
    }

    $mealIds = $order->meals()->pluck('id')->toArray();

    $address = $request->input('address', $order->address_id);
    $deliveryCost = $request->input('delivery_cost', $order->delivery_cost);
    $orderDate = Carbon::now()->tz('Europe/London')->addHours(2);

    // Check if the offer associated with the original order is still valid
    $offer = Offer::find($order->offer_id);
    if ($offer && $offer->end_date >= $orderDate->format('Y-m-d')) {
        $newOrder = Order::create([
            'address_id' => $address,
            'delivery_cost' => $deliveryCost,
            'order_price' => $order->order_price,
            'order_date' => $orderDate,
            'user_id' => $user->id,
            'offer_id' => $offer->id,
        ]);

        $newOrder->meals()->attach($mealIds, ['quantity' => 1]);

        return response()->json('Order reordered successfully', 201);
    } else {
        return response()->json('The offer associated with the original order is no longer available.', 400);
    }
}

public function getMostOrderedMeals()
{
    $mostOrderedMeals = DB::table('order_meal')
        ->select('meal_id', DB::raw('count(*) as total_orders'))
        ->groupBy('meal_id')
        ->orderByDesc('total_orders')
        ->limit(10)
        ->get();

    $mealIds = $mostOrderedMeals->pluck('meal_id')->toArray();

    $meals = Meal::whereIn('id', $mealIds)->with(['owner_resturent' => function ($query) {
            $query->select('id', 'resturent_name');
        }])->get();

    return response()->json($meals, 200);
}

public function updateOrderStatusToBeingProcessed($orderId)
{
    $order = Order::find($orderId);

    if (!$order) {
        return response()->json('Order not found', 404);
    }

    $order->status = 'Being_processed';
    $order->save();

    return response()->json('Order status updated to Being_processed', 200);
}

public function updateOrderStatusToReady($orderId)
{
    $order = Order::find($orderId);

    if (!$order) {
        return response()->json('Order not found', 404);
    }

    $order->status = 'Ready';
    $order->save();

    $deliveryManagers =User::whereHas('roles', function ($query) {
        $query->where('name', 'Delivery_manger');
    })->get();

    foreach ($deliveryManagers as $deliveryManager) {
        $message = [
            'title' =>'There are new Ready Orders',
            'body' =>'Check out ready-made orders',
        ];

        (new SendNotificationsService)->sendByFcm($deliveryManager->fcm_token, $message);
    }

    return response()->json('Order status updated to Ready', 200);
}



public function updateOrderStatusToDelivered($orderId)
{
    $order = Order::find($orderId);

    if (!$order) {
        return response()->json('Order not found', 404);
    }

    $order->status ='Delivered';
    $order->save();

    $user = User::find($order->user_id);
    $order_price=$order->order_price;

    $points =$order_price/ 10;
    $user->points += $points;
    $user->save();
    
    $user_id=$user->id;
    $fcmToken=User::where('id',$user_id)->value('fcm_token');
    
    $message = [
     'title' =>'You are earned Extra Points',
      'body' =>'Go to your profile and check Your Points',
        ];

   (new SendNotificationsService)->sendByFcm($fcmToken,$message);
    
    
    return response()->json('Order status updated to Delivered. Points earned: '.$points, 200);
}


public function calculateDailySalesForAllRestaurants()
{
    $dailySales = Order::selectRaw('owner_resturent_id, DATE(created_at) as date, SUM(total_amount) as total_sales')
   ->groupBy('owner_resturent_id', 'date')
   ->get();

    return response()->json($dailySales, 200);
}


public function calculateDistance(Request $request)
{
    $validator = Validator::make($request->all(), [
        'x1' => 'required|numeric',
        'y1' => 'required|numeric',
        'x2' => 'required|numeric',
        'y2' => 'required|numeric',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'error' => 'Invalid input data'
        ], 400);
    }

    $x1 = $request->input('x1');
    $y1 = $request->input('y1');
    $x2 = $request->input('x2');
    $y2 = $request->input('y2');

    try {
        $theta = $x1 - $x2;
        $dist = sin(deg2rad($y1)) * sin(deg2rad($y2)) + cos(deg2rad($y1)) * cos(deg2rad($y2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $distance = $dist * 60 * 1.1515 * 1.609344;

        $cost = (int) round($distance);

        $response = [
            'distance' => $distance,
            'cost' => $cost
        ];

        return response()->json($response);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error calculating distance and cost'
        ], 500);
    }
}

public function confirmDeliveryOrderByResturent(Request $request ,$orderId)
{
    $order = Order::findOrFail($orderId);

    if ($order->status == 'Under_delivery') {
        return response()->json([
            'error' => 'The order has already been Under_delivery'
        ], 400);
    }

    $order->delivery_received =true;
    $order->save();
    // Get the delivery person's information from the request
    $deliveryPersonId = $order->delivery_worker_id;
    $deliveryDate = Carbon::now()->tz('Europe/London')->addHours(2);


    // Log the delivery details
    Log::channel('result_report')->info('Order information', [
        'order_id' => $order->id,
        'delivery_worker_id' => $deliveryPersonId,
        'delivery_date' => $deliveryDate
    ]);

    return response()->json([
        'message' => 'Order delivered for delivery successfully'
    ], 200);
}


}