<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Models\Order;
use Validator;
use Carbon\Carbon;
use App\Models\Owner_resturent;
use App\Services\SendNotificationsService;

class AccountController extends Controller
{
public function addBankCard(Request $request)
{
    $validator = Validator::make(request()->all(), [
        'card_number' => 'required|min:10|max:10',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors()->toJson(), 400);
    }

    $user = auth()->user();

    if (Account::where('user_id', $user->id)->exists()) {
    return response()->json(['message' => 'You already have a bank card registered'], 400);
    }

    $newCard = new Account();
    $newCard->account = $request->input('account');
    $newCard->card_number = $request->input('card_number');
    $newCard->payment_date = Carbon::now()->tz('Europe/London')->addHours(3)->format('Y-m-d');
    $newCard->password = request()->password;
    $newCard->user_id = $user->id;
    $newCard->save();

    return response()->json(['message' => 'A bank card has been created successfully']);
}

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
    
    
    
    $owner_resturent_id=$order->owner_resturents->first()->id;
    $email=Owner_resturent::where('id',$owner_resturent_id)->value('email');
    $date=Carbon::now()->tz('Europe/London')->addHours(2)->format('Y-m-d');
    $fcmToken=User::where('email',$email)->value('fcm_token');
    $message=[
        'title'=>'there are new Order ',
        'body'=>Carbon::now()->tz('Europe/London')->addHours(2)->format('Y-m-d'),
    ];

    (new SendNotificationsService)->sendByFcm($fcmToken,$message);

    return response()->json(['message' => 'Payment was made and the amount was debited successfully']);
}

public function makePaymentByPoints($orderId)
{
    $order = Order::find($orderId);

    if (!$order) {
        return response()->json('Order not found', 404);
    }

    $user = User::find($order->user_id);
    $price = $order->order_price+$order->delivery_cost;

    $pointsNeeded =$price/ 10;
    $balance = $user->points;

    if ($balance >= $pointsNeeded) {
        $user->points -= $pointsNeeded;
        $order->paid = 1;
        $order->save();
        $user->save();
        
        $owner_resturent_id=$order->owner_resturents->first()->id;
    $email=Owner_resturent::where('id',$owner_resturent_id)->value('email');
    $date=Carbon::now()->tz('Europe/London')->addHours(2)->format('Y-m-d');
    $fcmToken=User::where('email',$email)->value('fcm_token');
    $message=[
        'title'=>'there are new Order ',
        'body'=>Carbon::now()->tz('Europe/London')->addHours(2)->format('Y-m-d'),
    ];

    (new SendNotificationsService)->sendByFcm($fcmToken,$message);

        return response()->json('Payment successful. Order status updated to Paid.', 200);
    } else {
        return response()->json('You dont have enough points to make the payment using points', 400);
    }
    

}

public function makePaymentByElecBank(Request $request, $orderId)
{
   
    $order = Order::find($orderId);

    if ($order->paid == 1) {
        return response()->json(['message' => 'The order is already paid.']);
    }

    $order->paid = 1;
    $order->save();
    
     
    $owner_resturent_id=$order->owner_resturents->first()->id;
    $email=Owner_resturent::where('id',$owner_resturent_id)->value('email');
    $date=Carbon::now()->tz('Europe/London')->addHours(2)->format('Y-m-d');
    $fcmToken=User::where('email',$email)->value('fcm_token');
    $message=[
        'title'=>'there are new Order ',
        'body'=>Carbon::now()->tz('Europe/London')->addHours(2)->format('Y-m-d'),
    ];

    (new SendNotificationsService)->sendByFcm($fcmToken,$message);

    return response()->json(['message' => 'Payment was made and the amount was debited successfully']);
   
}



}
