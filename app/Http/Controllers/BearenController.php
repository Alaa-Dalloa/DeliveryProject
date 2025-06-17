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
    $endOfPreviousWeek = Carbon::now()->tz('Europe/London')->addHours(2)->subWeek()->endOfWeek();
    $startOfPreviousWeek = $endOfPreviousWeek->copy()->subDays(6);

    $totalSalesByDay = Bearen::where('owner_resturent_id', $owner_resturent_id)
                        ->whereBetween('daily_sale_date', [
                            $startOfPreviousWeek->format('m-d'),
                            $endOfPreviousWeek->format('m-d'),
                        ])
                        ->selectRaw("daily_sale_date, SUM(total_sales) as total_sales")
                        ->groupBy('daily_sale_date')
                        ->get();

    $result = [];
    $currentDate = $startOfPreviousWeek;
    $endDate = $endOfPreviousWeek;

    while ($currentDate <= $endDate) {
        $totalSales = $totalSalesByDay->where('daily_sale_date', $currentDate->format('m-d'))->first()?->total_sales;
        $result[$currentDate->format('Y-m-d')] = $totalSales;
        $currentDate->addDay();
    }

    return response()->json($result);
}


public function getTotalSalesByYear(Request $request, $owner_resturent_id)
{
    $previousYear = Carbon::now()->tz('Europe/London')->addHours(2)->subYear()->year;
    $result = [];

    for ($month = 1; $month <= 12; $month++) {
        $startOfMonth = Carbon::create($previousYear, $month, 1, 0, 0, 0);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $totalSales = BearenM::where('owner_resturent_id', $owner_resturent_id)
                        ->whereYear('created_at', $previousYear)
                        ->whereMonth('created_at', $month)
                        ->sum('total_sales');

        $result[$startOfMonth->format('Y-m')] = $totalSales;
    }

    return response()->json($result);
}

public function getTotalSaleByMounth(Request $request, $owner_resturent_id)
{
    $previousMonth = Carbon::now()->tz('Europe/London')->addHours(2)->subMonth(1);
    $previousYear = $previousMonth->year;
    $previousMonthNumber = $previousMonth->month;

    // Get the number of days in the previous month
    $daysInPreviousMonth = $previousMonth->daysInMonth;

    $result = [];

    for ($i = 1; $i <= $daysInPreviousMonth; $i++) {
        $date = Carbon::create($previousYear, $previousMonthNumber, $i, 0, 0, 0);
        $startDate = $date->format('m-d');
        $endDate = $date->copy()->endOfDay()->format('m-d');

        $totalSalesByDay = Bearen::where('owner_resturent_id', $owner_resturent_id)
                            ->whereBetween('daily_sale_date', [
                                $startDate,
                                $endDate,
                            ])
                            ->selectRaw("SUM(total_sales) as total_sales")
                            ->value('total_sales');

        $result[$date->format('Y-m-d')] = $totalSalesByDay;
    }

    return response()->json($result);
}


public function getTotalSalesByWeekSuperAdmin(Request $request, $owner_resturent_id)
{
    $endOfPreviousWeek = Carbon::now()->tz('Europe/London')->addHours(2)->subWeek()->endOfWeek();
    $startOfPreviousWeek = $endOfPreviousWeek->copy()->subDays(6);

    $totalSalesByDay = Bearen::where('owner_resturent_id', $owner_resturent_id)
                        ->whereBetween('daily_sale_date', [
                            $startOfPreviousWeek->format('m-d'),
                            $endOfPreviousWeek->format('m-d'),
                        ])
                        ->selectRaw("daily_sale_date, SUM(total_summation) as total_summation")
                        ->groupBy('daily_sale_date')
                        ->get();

    $result = [];
    $currentDate = $startOfPreviousWeek;
    $endDate = $endOfPreviousWeek;

    while ($currentDate <= $endDate) {
        $total_summation = $totalSalesByDay->where('daily_sale_date', $currentDate->format('m-d'))->first()?->total_summation;
        $result[$currentDate->format('Y-m-d')] = $total_summation;
        $currentDate->addDay();
    }

    return response()->json($result);
}



public function getTotalSalesByYearForSuperAdmin(Request $request, $owner_resturent_id)
{
    $previousYear = Carbon::now()->tz('Europe/London')->addHours(2)->subYear()->year;
    $result = [];

    for ($month = 1; $month <= 12; $month++) {
        $startOfMonth = Carbon::create($previousYear, $month, 1, 0, 0, 0);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $totalSales = BearenM::where('owner_resturent_id', $owner_resturent_id)
                        ->whereYear('created_at', $previousYear)
                        ->whereMonth('created_at', $month)
                        ->sum('total_summation');

        $result[$startOfMonth->format('Y-m')] = $totalSales;
    }

    return response()->json($result);
}


public function GetTotalSaleByMounthForSuperAdmin(Request $request, $owner_resturent_id)
{
    $previousMonth = Carbon::now()->tz('Europe/London')->addHours(2)->subMonth(1);
    $previousYear = $previousMonth->year;
    $previousMonthNumber = $previousMonth->month;

    // Get the number of days in the previous month
    $daysInPreviousMonth = $previousMonth->daysInMonth;

    $result = [];

    for ($i = 1; $i <= $daysInPreviousMonth; $i++) {
        $date = Carbon::create($previousYear, $previousMonthNumber, $i, 0, 0, 0);
        $startDate = $date->format('m-d');
        $endDate = $date->copy()->endOfDay()->format('m-d');

        $totalSalesByDay = Bearen::where('owner_resturent_id', $owner_resturent_id)
                            ->whereBetween('daily_sale_date', [
                                $startDate,
                                $endDate,
                            ])
                            ->selectRaw("SUM(total_summation) as total_summation")
                            ->value('total_summation');

        $result[$date->format('Y-m-d')] = $totalSalesByDay;
    }

    return response()->json($result);
}

public function getTotalSalesByMonthTWO(Request $request, $owner_resturent_id)
{
    // Get the current month and year
    $currentMonth = Carbon::now()->tz('Europe/London')->addHours(2)->month;
    $currentYear = Carbon::now()->tz('Europe/London')->addHours(2)->year;

    // Get the previous month and year
    $previousMonth = Carbon::now()->tz('Europe/London')->addHours(2)->subMonth(1)->month;
    $previousYear = Carbon::now()->tz('Europe/London')->addHours(2)->subMonth(1)->year;

    // Get the number of days in the current and previous months
    $currentMonthDays = Carbon::create($currentYear, $currentMonth)->daysInMonth;
    $previousMonthDays = Carbon::create($previousYear, $previousMonth)->daysInMonth;

    $currentMonthSales = [];
    $previousMonthSales = [];

    // Get the total sales for each day in the current month
    for ($i = 1; $i <= $currentMonthDays; $i++) {
        $date = Carbon::create($currentYear, $currentMonth, $i, 0, 0, 0);
        $startDate = $date->format('m-d');
        $endDate = $date->copy()->endOfDay()->format('m-d');

        $totalSalesByDay = Bearen::where('owner_resturent_id', $owner_resturent_id)
                            ->whereBetween('daily_sale_date', [
                                $startDate,
                                $endDate,
                            ])
                            ->selectRaw("SUM(total_sales) as total_sales")
                            ->value('total_sales');

        $currentMonthSales[$date->format('Y-m-d')] = $totalSalesByDay;
    }

    // Get the total sales for each day in the previous month
    for ($i = 1; $i <= $previousMonthDays; $i++) {
        $date = Carbon::create($previousYear, $previousMonth, $i, 0, 0, 0);
        $startDate = $date->format('m-d');
        $endDate = $date->copy()->endOfDay()->format('m-d');

        $totalSalesByDay = Bearen::where('owner_resturent_id', $owner_resturent_id)
                            ->whereBetween('daily_sale_date', [
                                $startDate,
                                $endDate,
                            ])
                            ->selectRaw("SUM(total_sales) as total_sales")
                            ->value('total_sales');

        $previousMonthSales[$date->format('Y-m-d')] = $totalSalesByDay;
    }

    // Return the sales data as a JSON response
    return response()->json([
        'current_month_sales' => $currentMonthSales,
        'previous_month_sales' => $previousMonthSales
    ]);
}


public function getTotalSalesByMonthTWOForSuperAdmin(Request $request, $owner_resturent_id)
{
    // Get the current month and year
    $currentMonth = Carbon::now()->tz('Europe/London')->addHours(2)->month;
    $currentYear = Carbon::now()->tz('Europe/London')->addHours(2)->year;

    // Get the previous month and year
    $previousMonth = Carbon::now()->tz('Europe/London')->addHours(2)->subMonth(1)->month;
    $previousYear = Carbon::now()->tz('Europe/London')->addHours(2)->subMonth(1)->year;

    // Get the number of days in the current and previous months
    $currentMonthDays = Carbon::create($currentYear, $currentMonth)->daysInMonth;
    $previousMonthDays = Carbon::create($previousYear, $previousMonth)->daysInMonth;

    $currentMonthSales = [];
    $previousMonthSales = [];

    // Get the total sales for each day in the current month
    for ($i = 1; $i <= $currentMonthDays; $i++) {
        $date = Carbon::create($currentYear, $currentMonth, $i, 0, 0, 0);
        $startDate = $date->format('m-d');
        $endDate = $date->copy()->endOfDay()->format('m-d');

        $totalSalesByDay = Bearen::where('owner_resturent_id', $owner_resturent_id)
                            ->whereBetween('daily_sale_date', [
                                $startDate,
                                $endDate,
                            ])
                           ->selectRaw("SUM(total_summation) as total_summation")
                            ->value('total_summation');

        $currentMonthSales[$date->format('Y-m-d')] = $totalSalesByDay;
    }

    // Get the total sales for each day in the previous month
    for ($i = 1; $i <= $previousMonthDays; $i++) {
        $date = Carbon::create($previousYear, $previousMonth, $i, 0, 0, 0);
        $startDate = $date->format('m-d');
        $endDate = $date->copy()->endOfDay()->format('m-d');

        $totalSalesByDay = Bearen::where('owner_resturent_id', $owner_resturent_id)
                            ->whereBetween('daily_sale_date', [
                                $startDate,
                                $endDate,
                            ])
                          ->selectRaw("SUM(total_summation) as total_summation")
                            ->value('total_summation');

        $previousMonthSales[$date->format('Y-m-d')] = $totalSalesByDay;
    }

    // Return the sales data as a JSON response
    return response()->json([
        'current_month_sales' => $currentMonthSales,
        'previous_month_sales' => $previousMonthSales
    ]);
}

}


