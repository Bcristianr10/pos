<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return view('admin.payment_methods.index');
    }

    public function getPaymentMethods()
    {
        return PaymentMethod::where('active', true)->get();
    }

}
