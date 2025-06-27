<?php

namespace App\Services;

class MyFatoorahServiceMock
{
    public function createInvoice($order)
    {
        return [
            'IsSuccess' => true,
            'Message' => 'Mock invoice created successfully',
            'Data' => [
                'InvoiceURL' => 'https://mock.myfatoorah.com/invoice/' . uniqid()
            ]
        ];
    }
}
