<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bearen;
use App\Models\Owner_resturent;
use App\Models\meal;
use App\Models\Order;
use App\Models\BearenM;
use App\Models\BearenYear;
use Carbon\Carbon;

class BearenController extends Controller
{

public function getBearen($restaurantId)
{
    $bearen = Bearen::where('owner_resturent_id', $restaurantId)
        ->with(['owner_resturent' => function ($query) {
            $query->select('id', 'resturent_name');
        }])
        ->get();

    return response()->json($bearen, 200);
}

public function getAllBearen()
{
    $bearen = Bearen::with(['owner_resturent' => function ($query) {
            $query->select('id', 'resturent_name');
        }])
        ->get();
    return response()->json($bearen,200);
}

public function getRestaurantOrders($bearenid)
{  
    $owner_resturent_id = Bearen::where('id',$bearenid)->value('owner_resturent_id');
    $owner_resturent = Owner_resturent::find($owner_resturent_id);
    
    if ($owner_resturent) {
        return $owner_resturent->orders()->get();
    } else {
        return collect();
    }
}
public function getMonthlyInventory($restaurantId)
{
    $monthlyInventory = BearenM::select('monthly_sale_date', 'total_sales', 'total_delivery_cost', 'total_summation')
        ->where('owner_resturent_id', $restaurantId)
        ->get();

    return response()->json($monthlyInventory, 200);
}

public function getYearlyInventory($restaurantId)
{
    $yearlyInventory = BearenYear::select('annual_sale_date', 'total_sales', 'total_delivery_cost', 'total_summation')
        ->where('owner_resturent_id', $restaurantId)
        ->get();

    return response()->json($yearlyInventory, 200);
}


public function getAllBearenMonthly()
{
     $monthlyInventory = BearenM::select('monthly_sale_date', 'total_sales', 'total_delivery_cost', 'total_summation')
        ->get();

    return response()->json($monthlyInventory, 200);
}

public function getAllBearenYearly()
{
     $yearlyInventory = BearenYear::select('annual_sale_date', 'total_sales', 'total_delivery_cost', 'total_summation')
        ->get();

    return response()->json($yearlyInventory, 200);
}

public function getTotalSalesByWeek(Request $request, $owner_resturent_id)
{
    $startOfWeek = Carbon::now()->tz('Europe/London')->addHours(2)->startOfWeek();
    $endOfWeek = Carbon::now()->tz('Europe/London')->addHours(2)->endOfWeek();

    $totalSalesByDay = Bearen::where('owner_resturent_id', $owner_resturent_id)
                        ->whereBetween('daily_sale_date', [
                            $startOfWeek->format('m-d'),
                            $endOfWeek->format('m-d'),
                        ])
                        ->selectRaw("daily_sale_date, SUM(total_sales) as total_sales")
                        ->groupBy('daily_sale_date')
                        ->get();

    $result = [];
    $currentDate = $startOfWeek;
    $endDate = $endOfWeek;

    while ($currentDate <= $endDate) {
        $totalSales = $totalSalesByDay->where('daily_sale_date', $currentDate->format('m-d'))->first()?->total_sales ?? 0;
        $result[$currentDate->format('Y-m-d')] = $totalSales;
        $currentDate->addDay();
    }

    return response()->json($result);
}
public function getTotalSalesByYear(Request $request, $owner_resturent_id)
{
    $currentYear = Carbon::now()->tz('Europe/London')->addHours(2)->year;
    $result = [];

    for ($month = 1; $month <= 12; $month++) {
        $startOfMonth = Carbon::create($currentYear, $month, 1, 0, 0, 0);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $totalSales = BearenM::where('owner_resturent_id', $owner_resturent_id)
                        ->whereYear('created_at', $currentYear)
                        ->whereMonth('created_at', $month)
                        ->sum('total_sales');

        $result[$startOfMonth->format('Y-m')] = $totalSales;
    }

    return response()->json($result);
}


}



