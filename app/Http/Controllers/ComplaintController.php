<?php

namespace App\Http\Controllers;
use Validator;
use App\Models\User;
use App\Models\Order;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SendNotificationsService;


class ComplaintController extends Controller
{
    public function addComplaint(Request $request , $orderId)
    {

    $delivery_worker_id = Order::where('id', $orderId)->value('delivery_worker_id');
    $user_id=Auth::user()->id;
    $complaint = new Complaint;
    $complaint->complaint = $request->input('complaint');
    $complaint->order_id = $orderId;
    $complaint->delivery_worker_id=$delivery_worker_id;
    $complaint->user_id =$user_id;
    $complaint->save();
 
    $deliveryManagers =User::whereHas('roles', function ($query) {
        $query->where('name','Delivery_manger');
    })->get();

    foreach ($deliveryManagers as $deliveryManager) {
        $message = [
            'title' => 'There are new complaint',
            'body' =>$complaint->complaint,
        ];

        (new SendNotificationsService)->sendByFcm($deliveryManager->fcm_token,$message);

}
 return response()->json('complaint added successfully', 200);

}

public function allComplaint()
{

  $allComplaints=Complaint::all();
        return response()->json([$allComplaints], 200);
}

public function DetailComplaint($complaintId)

{

$complaint=Complaint::where('id',$complaintId)->get();
$orderId=Complaint::where('id',$complaintId)->value('order_id');
$order=Order::where('id',$orderId)->get();
$delivery_worker_id=Complaint::where('id',$complaintId)->value('delivery_worker_id');
$user=User::where('id',$delivery_worker_id)->get();
 return response()->json(['complaint'=>$complaint,
'order'=>$order,
'delivery_worker'=>$user
], 200);

}

public function getComplaintByWorker($delivery_worker_id){

$complaint=Complaint::where('delivery_worker_id',$delivery_worker_id)->get();
 return response()->json([$complaint], 200);

}

}
