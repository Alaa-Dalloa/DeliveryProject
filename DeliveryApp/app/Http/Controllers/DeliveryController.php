<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{

   public function AddAccountDeliveryManger(Request $request) {
       $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:5',
            'phone'=> 'required|min:10',
        ]);
 
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = new User;
        $user->name = request()->name;
        $user->email = request()->email;
        $user->phone = request()->phone;
        $user->password = bcrypt(request()->password);
        $user->save();
        $customerRole = Role::where('name', 'Delivery_manger')->first();  
        $user->roles()->attach($customerRole);
        return response()->json($user, 201);
    }


    public function AddAccountDelivery(Request $request) {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:5',
            'phone'=> 'required|min:10',
        ]);
 
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
 
        $user = new User;
        $user->name = request()->name;
        $user->email = request()->email;
        $user->phone = request()->phone;
        $user->password = bcrypt(request()->password);
        $user->save();
        $customerRole = Role::where('name', 'Delivery_worker')->first();  
        $user->roles()->attach($customerRole);
        return response()->json($user, 201);
    }

 public function getActiveDeliveryWorkers()
{
    $role = Role::where('name', 'Delivery_worker')->first();

    if ($role) {
        $currentWeek = date('W');

        $deliveryWorkers = $role->users()->get();
        $totalDeliveryWorkers = $deliveryWorkers->count();
        $activeDeliveryWorkers = collect();

        foreach ($deliveryWorkers as $index => $deliveryWorker) {
            $status = ($currentWeek % 2 == 0) ? (($index < $totalDeliveryWorkers / 2) ? 'Active' : 'Inactive') : (($index < $totalDeliveryWorkers / 2) ? 'Inactive' : 'Active');
            $deliveryWorker->status = $status;
            $deliveryWorker->save();

            if ($status == 'Active') {
                $activeDeliveryWorkers->push($deliveryWorker);
            }
        }

        return $activeDeliveryWorkers;
    } else {
        return collect();
    }
}

   public function Set_not_available($deliveryId)

    {
      $deliveryWorker = User::find($deliveryId);

      if ($deliveryWorker->status == 'Inactive') {

      return response()->json(['message' => 'delivery Worker is already NotAvailable'], 200);
      }

      $deliveryWorker->status = 'Inactive';
      $deliveryWorker->save();

      return response()->json(['message' => 'delivery Worker is Not Available now'], 200);
    }

    public function Set_available($deliveryId)

    {
      $deliveryWorker = User::find($deliveryId);

      if ($deliveryWorker->status == 'Active') {

      return response()->json(['message' => 'delivery Worker is already Available'], 200);
      }

      $deliveryWorker->status = 'Active';
      $deliveryWorker->save();

      return response()->json(['message' => 'delivery Worker is Available now'], 200);
    }



  public function cancelDeliveryWorker($deliveryId)
{
    $delivery = User::find($deliveryId);
    
    if ($delivery) {
        $deliveryWorkerRole = Role::where('name', 'delivery_worker')->first();
        
        if ($deliveryWorkerRole) {
            $delivery->roles()->detach($deliveryWorkerRole->id);

            return response()->json("The delivery worker role has been removed from the user successfully.");
        } else {
            return response()->json("Delivery worker role not found.");
        }
    } else {
        return response()->json("User not found.");
    }
}

public function updateDelivery(Request $request, $id)
{
    $delivery = User::find($id);

    if ($delivery) {
        if ($request->filled('name')) {
            $delivery->name = $request->input('name');
        }

        if ($request->filled('email')) {
            $delivery->email = $request->input('email');
        }

        if ($request->filled('password')) {
            $delivery->password = bcrypt(request()->password);
        }

        if ($request->filled('phone')) {
            $delivery->phone = $request->input('phone');
        }

        $result = $delivery->save();

        if ($result) {
            return ["Result" => "Data has been updated"];
        }

        return ["Result" => "Operation failed"];
    }

    return ["Result" => "delivery not found"];
}


public function updateStatusDelivery_received($Id) {

    $order = Order::find($Id);
    $user =  Auth::user()->id;
    if ($order && $user) {
        $order->delivery_received = true;
        $order->delivery_worker_id = $user;
        $order->status='Under_delivery';
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
            'message' => 'Done!'
        ]);
    } else {
        return response()->json([
            'error' => 'The order not found or user authorized'
        ], 404);
    }
   
}

public function assignOrderToDelivery($orderId, $deliveryId) {

    $order = Order::find($orderId);
    $deliveryPerson = User::find($deliveryId);

    if ($order && $deliveryPerson) {
        if ($deliveryPerson->status=='Active' && $deliveryPerson->roles()->where('name', 'Delivery_worker')->exists()) {
            $order->delivery_worker_id = $deliveryPerson->id;
            $order->save();

            return response()->json([
                'message' => 'Done! order assigned to delivery worker'
            ]);
        }
         else {
            return response()->json([
                'error' => 'The Delivery not Active or dont have permission to be delivery worker'
            ], 404);
        }

}
 else {
            return response()->json([
                'error' => 'Failed' ], 404);
        }


}

}
