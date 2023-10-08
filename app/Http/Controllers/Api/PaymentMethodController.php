<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $banks = PaymentMethod::where('status', 'active')
                                ->where('code', '!=', 'sha')
                                ->get()
                                ->map(function($bank) {
                                    $bank->thumbnail = $bank->thumbnail ? url($bank->thumbnail) : "";
                                    return $bank;
                                });

        return response()->json($banks);
    }
}
