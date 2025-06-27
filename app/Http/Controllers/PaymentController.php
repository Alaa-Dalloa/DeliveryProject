<?php

namespace App\Http\Controllers;
use App\Services\MyFatoorahServiceMock;
use Illuminate\Http\Request;
use App\Models\OrderTest;

class PaymentController extends Controller
{
public function createOrder(Request $request)
{
    $order = OrderTest::create($request->only('name', 'email', 'amount'));

    $paymentService = new MyFatoorahServiceMock();
    $result = $paymentService->createInvoice($order);

    return response()->json(['payment_url' => $result['Data']['InvoiceURL']]);
}
}
