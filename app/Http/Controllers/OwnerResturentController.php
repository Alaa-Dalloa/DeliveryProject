<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\Request;
use App\Models\Owner_resturent;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OwnerResturentController extends Controller
{
    public function AddAccountResturent(Request $request) {
       $validator = Validator::make(request()->all(), [
        'owner_name' => 'required',
        'resturent_name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed|min:5',
        'owner_phone'=> 'required|min:10',
        'resturent_phone'=> 'required|min:10',
        ]);
 
        if($validator->fails()){
        return response()->json($validator->errors()->toJson(), 400);
        }
 
        $resturent = new Owner_resturent;
        $resturent->owner_name = request()->owner_name;
        $resturent->resturent_name = request()->resturent_name;
        $resturent->email = request()->email;
        $resturent->owner_phone = request()->owner_phone;
        $resturent->resturent_phone = request()->resturent_phone;
        $resturent->password = request()->password;
        $resturent->save();

        $user = new User;
        $user->name =$resturent->owner_name;
        $user->email = $resturent->email;
        $user->phone =$resturent->resturent_phone;
        $user->password = bcrypt(request()->password);
        $user->save();
        $customerRole = Role::where('name', 'Restaurant_manager')->first();  
        $user->roles()->attach($customerRole);
        return response()->json(['message' => 'Restaurant information has been added successfully'], 201);
    }

   

public function getOwner_resturent()
{
    $Owner_resturents = Owner_resturent::select('id', 'owner_name', 'resturent_name', 'owner_phone', 'resturent_phone', 'status')->get();
    
    $now = Carbon::now('Europe/London')->addHours(2);
    $openTime = Carbon::createFromTime(10, 30, 0);
    $closeTime = Carbon::createFromTime(3, 0, 0)->addDays(1);
    
    foreach ($Owner_resturents as $resturent) {
        if ($closeTime->lessThan($openTime)) {
            if ($now->greaterThanOrEqualTo($openTime) || $now->lessThan($closeTime)) {
                $resturent->status = 'now_open';
            } else {
                $resturent->status = 'closed'; 
            }
        } else {
            if ($now->greaterThanOrEqualTo($openTime) && $now->lessThan($closeTime)) {
                $resturent->status =  'now_open'; 
            } else {
                $resturent->status =  'closed';
            }
        }

        // Check if the restaurant has any updated status
        $updatedResturent = Owner_resturent::find($resturent->id);
        if ($updatedResturent && !is_null($updatedResturent->status)) {
            $resturent->status = $updatedResturent->status;
        }
    }
    
    return response()->json(['Owner_resturents' => $Owner_resturents], 200);
}

    public function SearchOfResturent($resturent_name)

    {
        return Owner_resturent::select('id', 'owner_name', 'resturent_name', 'owner_phone', 'resturent_phone','status')->where("resturent_name","like","%".$resturent_name."%")->get();

    }

    public function updateRasturent ($id,Request $request)
 { 

   $resturent= Owner_resturent::find($id);
   $resturent->status=request()->status;
   $result=$resturent->save();
   if($result){
    return ["Result"=>"data has been updated"];
 }
 return ["Result"=>"operation failed"];
 }

 public function showDetailResturent($id)
{
    $Owner_resturent = Owner_resturent::find($id);

    if ($Owner_resturent) {
        return $Owner_resturent;
    } else {
        return response()->json(['message' => 'The Owner_resturent not found']);
    }
}

public function GetIdResturentByToken()
{
    $token = Auth::user()->email;
    
    $restaurant = DB::table('owner_resturents')
        ->where('email', $token)
        ->first();

    if ($restaurant) {
        return $restaurant->id;
    } else {
        return null;
    }
}



 
}
