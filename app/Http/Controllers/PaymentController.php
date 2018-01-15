<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;

use Paystack;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\Utilities;


class PaymentController extends Controller
{

    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway(Request $request)
    {

        $save_history = [
            'id' => uniqid(),
            'user_id' => \Session::get('agency_id'),
            'transaction_type' => 'CREDIT',
            'amount' => ($request->amount / 100),
        ];
        $wallet_history = Utilities::switch_db('api')->table('walletHistories')->insert($save_history);
        return Paystack::getAuthorizationUrl()->redirectNow();
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();
        $save_wallet = [
            'id' => uniqid(),
            'user_id' => \Session::get('agency_id'),
            'current_balance' => $paymentDetails['data']['amount'] / 100,
            'status' => $paymentDetails['status'],
        ];

        $save_wallet_db = Utilities::switch_db('api')->table('wallets')->insert($save_wallet);
        $update_walletHistory = Utilities::switch_db('api')->select("UPDATE walletHistories SET status = 'SUCCESSFUL'");

        return redirect()->route('agency_wallet.statement')->with('success', 'Payment Successful');

    }
}