<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Str;

class TopUpController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->only('amount', 'pin', 'payment_method_code');

        $validator = Validator::make($data, [
            'amount' => 'required|integer|min:10000',
            'pin' => 'required|digits:6',
            'payment_method_code' => 'required|in:bni_va,bca_va,bri_va',
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $pinChecker = pinChecker($request->pin);
        if(!$pinChecker){
            return response()->json(['errors' => 'Your PIN is wrong'], 400);
        }

        $transactionType = TransactionType::where('code', 'top_up')->first();
        $paymentMethod = PaymentMethod::where('code', $request->payment_method_code)->first();

        DB::beginTransaction();

        try {
            $transaction = Transaction::create([
                'user_id' => auth()->user()->id,
                'payment_method_id' => $paymentMethod->id,
                'transaction_type_id' => $transactionType->id,
                'amount' => $request->amount,
                'transaction_code' => strtoupper(Str::random(10)),
                'description' => 'Top Up via ' . $paymentMethod->name,
                'status' => 'pending',
            ]);

            $params = $this->buildMidtransParams([
                'transaction_code' => $transaction->transaction_code,
                'amount' => $transaction->amount,
                'payment_method_code' => $paymentMethod->code,
            ]);
            //dd($this->callMidtrans($params));
            // call to midtrans
            $midtrans = $this->callMidtrans($params);

            DB::commit();
            // dd($midtrans);
            return response()->json($midtrans);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    private function callMidtrans($params)
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION');
        \Midtrans\Config::$isSanitized = (bool) env('MIDTRANS_IS_SANITIZED');
        \Midtrans\Config::$is3ds = (bool) env('MIDTRANS_IS_3DS');

        $createTranaction = \Midtrans\Snap::createTransaction($params);

        return [
            'redirect_url' => $createTranaction->redirect_url,
            'token' => $createTranaction->token
        ];
    }

    private function buildMidtransParams(array $params)
    {
        $transactionDetails = [
            'order_id' => $params['transaction_code'],
            'gross_amount' => $params['amount'],
        ];

        $user = auth()->user();
        $splitName = $this->splitName($user->name);
        $customerDetails = [
            'first_name' => $splitName['first_name'],
            'last_name' => $splitName['last_name'],
            'email' => $user->email,
        ];

        $enablePayment = [
            $params['payment_method_code']
        ];

        //dd('a',$transactionDetails, 'b',$customerDetails, 'c',$enablePayment);
        return [
            'transaction_details' => $transactionDetails,
            'customer_details' => $customerDetails,
            'enabled_payments' => $enablePayment
        ];
    }

    // split name, first and lastname
    private function splitName($fullname)
    {
        $name = explode(' ', $fullname);
        $lastname = count($name) > 1 ? array_pop($name) : $fullname;
        $firstname = implode(' ', $name);

        return [
            'first_name' => $firstname,
            'last_name' => $lastname
        ];
    }
}
