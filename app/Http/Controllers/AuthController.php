<?php
 
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Address;
use App\Models\Role;
use App\Models\Owner_resturent;
use App\Models\Complaint;
use Validator;
use Illuminate\Http\Request;
use JWTAuth;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Auth\AuthenticationException;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

 
class AuthController extends Controller
{
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register() {
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
        $customerRole = Role::where('name', 'customer')->first();  
        $user->roles()->attach($customerRole);
        return response()->json($user, 201);
    }
        
 
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);
 
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user=User::where('email','=',$request->email)->get();
        return [
            'token'=>$token,
            'user'=>$user
            ];
    }
 
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        $user = auth()->user();
        $addresses = $user->addresses;
        return response()->json([
            'user' => $user
        ]);
    }
 
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
 
        return response()->json(['message' => 'Successfully logged out']);
    }
 
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

public function addAddress(Request $request)
{
    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    if ($user->roles()->where('name', 'Restaurant_manager')->exists()) {
        // Check if the user already has an associated address
        if ($user->addresses()->count() > 0) {
            return response()->json(['error' => 'Restaurant managers can only have one address'], 400);
        }
    }

    $address = new Address;
    $address->y = $request->y;
    $address->x = $request->x;
    $address->address_name = $request->address_name;
    $address->save();

    $user->addresses()->attach($address);

    return response()->json(['message' => 'Address added successfully'], 200);
}

public function updateAddress(Request $request, $addressId)
{
    $user = Auth::user();

    // Apply rate limiting
    $key = 'updateAddress_' . $user->id;
    $maxAttempts = 100; // 100 requests per minute
    $decaySeconds = 60; // 60 seconds (1 minute)

    if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
        $seconds = RateLimiter::availableIn($key);
        return response()->json([
            'errors' => 'Too many requests. Please try again after ' . $seconds . ' seconds.'
        ], 429);
    }

    RateLimiter::hit($key, $decaySeconds);

    $address = Address::find($addressId);

    if (!$address) {
        return response()->json(['error' => 'Address not found'], 404);
    }

    if (!$user->addresses->contains($addressId)) {
        return response()->json(['error' => 'You do not have permission to do this'], 403);
    }

    $address->y = $request->y;
    $address->x = $request->x;
    $address->address_name = $request->address_name;
    $address->save();

    return response()->json(['message' => 'Address updated successfully'], 200);
}

public function deleteAddress(Request $request, $addressId)
{
    $user = Auth::user();
    $address = Address::find($addressId);

    if (!$address) {
        return response()->json(['error' => 'Address not found'], 404);
    }

    if (!$user->addresses->contains($addressId)) {
        return response()->json(['error' => 'You do not have permission to do this'], 403);
    }

    // Check if the address is associated with an order
    if ($address->orders()->count() > 0) {
        return response()->json(['error' => 'Cannot delete an address associated with an order'], 400);
    }

    $address->delete();

    return response()->json(['message' => 'Address deleted successfully'], 200);
}

public function getUserAddresses()
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    $addresses = $user->addresses;

    return response()->json($addresses, 200);
}
public function getCustomers()
{
    $role = Role::where('name', 'Customer')->first();

    if ($role) {
        return $role->users()->get();
    } else {
        return collect();
    }
}


public function blockUser($userId)
{
    $user = User::find($userId);
     
    if ($user->blocked_until && $user->blocked_until > Carbon::now()->tz('Europe/London')->addHours(2)) {
        $remainingTime = Carbon::parse($user->blocked_until)->diffForHumans();
        return response()->json(['message' => 'User is already blocked until '.$remainingTime], 200);
    }

    $blockedUntil = Carbon::now()->tz('Europe/London')->addHours(2)->addDay();
    $user->blocked_until = $blockedUntil;
    $user->blocked=1;
    $user->save();

    return response()->json(['message' => 'User blocked successfully until '.$blockedUntil], 200);
}


public function getAddress($restaurantId)
{
    $email=Owner_resturent::where('id',$restaurantId)->value('email');
   $user_id=User::where('email',$email)->value('id');

    $restaurantAddress = DB::table('address_user')
        ->join('addresses', 'address_user.address_id', '=', 'addresses.id')
        ->where('address_user.user_id', $user_id)
        ->select('addresses.address_name', 'addresses.x', 'addresses.y')
        ->first();

    if ($restaurantAddress) {
        return [
            'address_name' => $restaurantAddress->address_name,
            'x' => $restaurantAddress->x,
            'y' => $restaurantAddress->y
        ];
    } else {
        return null;
    }
}

public function DetaileAddress(Request $request, $addressId)
{

    $address = Address::find($addressId);

    if (!$address) {
        return response()->json(['error' => 'Address not found'], 404);
    }

    return response()->json([$address], 200);


}

public function getUserInfo($userId)
{
    $user=User::where('id',$userId)->get();
    $complaints = Complaint::where('delivery_worker_id', $userId)->get();
    return response()->json([
        'user' => $user,
        'complaints' => $complaints
    ], 200);
}


public function getDeliveryWorker(){
    $role = Role::where('name', 'Delivery_worker')->first();

    if ($role) {
        $deliveryWorkers = $role->users()->get();
        $activeWorkers = collect();
        $inactiveWorkers = collect();
        $currentWeek = Carbon::now()->tz('Europe/London')->addHours(2)->weekOfYear;

        foreach ($deliveryWorkers as $worker) {
            if ($currentWeek % 2 == 0) { // الأسبوع الزوجي
                if ($worker->id % 2 == 0) { // الأرقام الزوجية نشطة
                    $activeWorkers->push($worker);
                } else { // الأرقام الفردية غير نشطة
                    $inactiveWorkers->push($worker);
                }
            } else { // الأسبوع الفردي
                if ($worker->id % 2 != 0) { // الأرقام الفردية نشطة
                    $activeWorkers->push($worker);
                } else { // الأرقام الزوجية غير نشطة
                    $inactiveWorkers->push($worker);
                }
            }
        }

        return $role->users()->get();
    } else {
        return collect();
    }
}


public function getDeliveryAddresses($deliveryId, Request $request)
{
    // تطبيق Rate Limiting
    $key = 'getDeliveryAddresses_' . $deliveryId;
    $maxAttempts = 100; // 100 طلب في الدقيقة الواحدة
    $decaySeconds = 60; // 60 ثانية (دقيقة واحدة)

    if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
        $seconds = RateLimiter::availableIn($key);
        return response()->json([
            'errors' => 'Too many requests. Please try again after ' . $seconds . ' seconds.'
        ], 429);
    }

    RateLimiter::hit($key, $decaySeconds);

    // باقي الكود
    try {
        $status = User::where('id', $deliveryId)->value('status');
        if ($status == 'Active') {
            $addresses = DB::table('address_user')
                ->join('addresses', 'address_user.address_id', '=', 'addresses.id')
                ->where('address_user.user_id', $deliveryId)
                ->select('addresses.address_name', 'addresses.x', 'addresses.y')
                ->get();

            if (count($addresses) > 0) {
                return response()->json($addresses, 200);
            } else {
                return response()->json(['errors' => 'No delivery addresses found'], 404);
            }
        } else {
            return response()->json(['errors' => 'Delivery person is not active'], 400);
        }
    } catch (AuthenticationException $e) {
        return response()->json(['errors' => 'Invalid token'], 401);
    }
}

public function saveFcmToken(Request $request)
    {
        $user = auth()->user();

        $user->fcm_token = $request->input('fcm_token');
        $user->save();

        return response()->json([
            'message' => 'FCM token saved successfully'
        ]);
    }

}