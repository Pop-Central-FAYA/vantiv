<?php

namespace Vanguard\Http\Controllers\Agency;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\Utilities;
use Yajra\DataTables\DataTables;
use Session;

class WalletsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agency_id = \Session::get('agency_id');
        if($agency_id != null){
            $user_id = $agency_id;
        }else{
            $user_id = Session::get('advertiser_id');
        }
        $wallets = Utilities::switch_db('api')->select("SELECT SUM(current_balance) as balance from wallets where user_id = '$user_id'");
        $wallet_history = Utilities::switch_db('api')->select("SELECT * from walletHistories where user_id = '$user_id'");
        $transaction = Utilities::switch_db('api')->select("SELECT * from transactions WHERE user_id = '$user_id' ORDER BY time_created DESC LIMIT 1");
        return view('wallets.wallet_statement')->with('wallet', $wallets)->with('history', $wallet_history)->with('transaction', $transaction)->with(['agency_id' => $agency_id, 'advertiser_id' => Session::get('advertiser_id')]);
    }

    public function getData(Datatables $datatables)
    {
        $agency_id = \Session::get('agency_id');
        if($agency_id != null){
            $user_id = $agency_id;
        }else{
            $user_id = Session::get('advertiser_id');
        }
        $trans = Utilities::switch_db('api')->select("SELECT * from transactions where user_id = '$user_id' ORDER BY time_created desc");
        $j = 1;
        $transaction = [];

        foreach ($trans as $trans)
        {
            $transaction[] = [
                'id' => $j,
                'reference' => $trans->reference,
                'type' => $trans->type,
                'amount' => '&#8358;'.number_format($trans->amount, 2),
                'date' => date('d/m/Y', strtotime($trans->time_created))
            ];
            $j++;
        }
        return $datatables->collection($transaction)
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $agency_id = \Session::get('agency_id');
        if($agency_id != null){
            $user_id = $agency_id;
            $agent_user_id = Utilities::switch_db('api')->select("SELECT user_id from agents where id = '$user_id'");
            $user = $agent_user_id[0]->user_id;
            $user_det = Utilities::switch_db('api')->select("SELECT * from users where id = '$user'");
        }else{
            $user_id = Session::get('advertiser_id');
            $user_det = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from advertisers where id = '$user_id')");
        }
        $wallets = Utilities::switch_db('api')->select("SELECT SUM(current_balance) as balance from wallets where user_id = '$user_id'");

        return view('wallets.create')->with('wallet', $wallets)->with('user_det', $user_det)->with('agency_id', $agency_id)->with('advertiser_id', Session::get('advertiser_id'))->with('user_id', $user_id);
    }

    public function pay(Request $request)
    {

        $agency_id = \Session::get('agency_id');
        if($agency_id != null){
            $user_id = $agency_id;
        }else{
            $user_id = Session::get('advertiser_id');
        }
        $insert = [
            'id' => uniqid(),
            'user_id' => $user_id,
            'reference' => $request->reference,
            'amount' => $request->amount,
            'status' => 'PENDING',
        ];

        $transaction = Utilities::switch_db('api')->table('transactions')->insert($insert);

        $response = $this->query_api_transaction_verify($request->reference);

        if($response['status'] === true){

            $amount = ($response['data']['amount']/100);
            $card = $response['data']['authorization']['card_type'];
            $status = $response['data']['status'];
            $message = $response['message'];
            $reference = $response['data']['reference'];
            $ip_address = $response['data']['ip_address'];
            $fees = $response['data']['fees'];
            $user_id = $user_id;
            $type = 'FUND WALLET';

            $update_transaction = Utilities::switch_db('api')->select("UPDATE transactions SET card_type = '$card', status = 'SUCCESSFUL', ip_address = '$ip_address', fees = '$fees', `type` = '$type', message = '$message' WHERE reference = '$reference'");

            if($transaction) {
                $this->updateWallet($amount);
                $msg = 'Your wallet has been funded with NGN'. $amount;
                return redirect()->back()->with('success', $msg);
            }

        }else{
            return back()->with('error', 'Sorry, something went wrong! Please contact the Administrator or Bank.');
        }


    }

    public function updateWallet($amount = 0)
    {
        $agency_id = \Session::get('agency_id');
        if($agency_id != null){
            $user_id = $agency_id;
        }else{
            $user_id = Session::get('advertiser_id');
        }

        $wallet = Utilities::switch_db('api')->select("SELECT * from wallets where user_id = '$user_id'");

        if($wallet){
            $prev_balance = $wallet[0]->current_balance;
            $current_balance = $amount + $prev_balance;
            $update_wallet = Utilities::switch_db('api')->select("UPDATE wallets set prev_balance = '$prev_balance', current_balance = '$current_balance' WHERE user_id = '$user_id'");

            $prev_bal = $wallet[0]->current_balance;
            $insert_history = [
                'id' => uniqid(),
                'user_id' => $user_id,
                'amount' => $amount,
                'prev_balance' => $prev_bal,
                'current_balance' => $amount + $prev_bal,
                'status' => 1,
            ];
            $add_walletHistory = Utilities::switch_db('api')->table('walletHistories')->insert($insert_history);
        }else {
            $wallet =
                [
                    'id' => uniqid(),
                    'user_id' => $user_id,
                    'prev_balance' => 0,
                    'current_balance' => $amount
                ];

            $insert_wallets = Utilities::switch_db('api')->table('wallets')->insert($wallet);
            $prev_bal = 0;
            $insert_history = [
                'id' => uniqid(),
                'user_id' => $user_id,
                'amount' => $amount,
                'prev_balance' => $prev_bal,
                'current_balance' => $amount + $prev_bal,
                'status' => 1,
            ];
            $add_walletHistory = Utilities::switch_db('api')->table('walletHistories')->insert($insert_history);
        }

    }

    protected function query_api_transaction_verify($reference)
    {
        $result = array();
        $url = 'https://api.paystack.co/transaction/verify/'.$reference;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer sk_test_485de9008374bbad12f121fefe3afe01d1568fbd']
        );
        $request = curl_exec($ch);
        curl_close($ch);

        if ($request) {
            $result = json_decode($request, true);
            return $result;
        }else {
            return false;
        }


    }
}
