<?php

namespace Vanguard\Http\Controllers\Agency;

use Illuminate\Http\Request;
use Monolog\Processor\UidProcessor;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\Api;
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
            $agent_user_id = Utilities::switch_db('api')->select("SELECT user_id from agents where id = '$user_id'");
            $user = $agent_user_id[0]->user_id;
            $user_det = Utilities::switch_db('api')->select("SELECT * from users where id = '$user'");
        }else{
            $user_id = Session::get('advertiser_id');
            $user_det = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from advertisers where id = '$user_id')");
        }
        $wallets = Utilities::switch_db('api')->select("SELECT SUM(current_balance) as balance from wallets where user_id = '$user_id'");
        $wallet_history = Utilities::switch_db('api')->select("SELECT * from walletHistories where user_id = '$user_id'");
        $transaction = Utilities::switch_db('api')->select("SELECT * from transactions WHERE user_id = '$user_id' ORDER BY time_created DESC ");

        return view('wallets.wallet_statement')->with('wallet', $wallets)->with('user_det', $user_det)->with('user_id', $user_id)->with('history', $wallet_history)->with('transactions', $transaction)->with(['agency_id' => $agency_id, 'advertiser_id' => Session::get('advertiser_id')]);
    }

    public function getData(Datatables $datatables, Request $request)
    {
        $agency_id = \Session::get('agency_id');
        if ($agency_id != null) {
            $user_id = $agency_id;
        } else {
            $user_id = Session::get('advertiser_id');
        }

        if($request->has('start_date') && $request->has('stop_date')){
            $start_date = $request->start_date;
            $stop_date = $request->stop_date;
            $trans = Utilities::switch_db('api')->select("SELECT * from transactions where user_id = '$user_id' AND time_created BETWEEN '$start_date' AND '$stop_date' ORDER BY time_created desc");

            $j = 1;
            $transaction = [];

            foreach ($trans as $trans) {
                $transaction[] = [
                    'id' => $j,
                    'reference' => strtoupper($trans->reference),
                    'type' => strtolower($trans->type),
                    'amount' => '&#8358;'.number_format($trans->amount, 2),
                    'date' => date('d/m/Y', strtotime($trans->time_created))
                ];
                $j++;
            }
            return $datatables->collection($transaction)
                ->make(true);
        }

        $trans = Utilities::switch_db('api')->select("SELECT * from transactions where user_id = '$user_id' ORDER BY time_created desc");
        $j = 1;
        $transaction = [];

        foreach ($trans as $trans) {
            $transaction[] = [
                'id' => $j,
                'reference' => strtoupper($trans->reference),
                'type' => strtolower($trans->type),
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
        $user_id = $agency_id;
        $agent_user_id = Utilities::switch_db('api')->select("SELECT user_id from agents where id = '$user_id'");
        $user = $agent_user_id[0]->user_id;
        $user_det = Utilities::switch_db('api')->select("SELECT * from users where id = '$user'");

        $wallets = Utilities::switch_db('api')->select("SELECT SUM(current_balance) as balance from wallets where user_id = '$user_id'");

        return view('wallets.create')->with('wallet', $wallets)->with('user_det', $user_det)->with('agency_id', $agency_id)->with('advertiser_id', Session::get('advertiser_id'))->with('user_id', $user_id);
    }

    public function pay(Request $request)
    {

        $agency_id = \Session::get('agency_id');
        $insert = [
            'id' => uniqid(),
            'user_id' => $agency_id,
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
            $user_id = $agency_id;
            $type = 'FUND WALLET';

            $update_transaction = Utilities::switch_db('api')->select("UPDATE transactions SET card_type = '$card', status = 'SUCCESSFUL', ip_address = '$ip_address', 
                                                                          fees = '$fees', `type` = '$type', message = '$message' WHERE reference = '$reference'");

            if ($transaction) {
                $user_agent = $_SERVER['HTTP_USER_AGENT'];
                $description = 'Wallet credited with '.$amount.' by '.$user_id;
                $ip = request()->ip();
                $user_activity = Api::saveActivity($user_id, $description, $ip, $user_agent);
                $update_wallet = $this->updateWallet($amount);
                if($update_wallet === 'success'){
                    $msg = 'Your wallet has been funded with NGN'. $amount;
                    Session::flash('success', $msg);
                    return redirect()->back();
                }else{
                    Session::flash('error', 'Sorry, something went wrong! Please contact the Administrator or Bank.');
                    return redirect()->back();
                }

            }

        } else {
            Session::flash('error', 'Sorry, something went wrong! Please contact the Administrator or Bank.');
            return redirect()->back();
        }


    }

    public function updateWallet($amount = 0)
    {
        $agency_id = \Session::get('agency_id');
        $wallet = Utilities::switch_db('api')->select("SELECT * from wallets where user_id = '$agency_id'");

        Utilities::switch_db('api')->beginTransaction();

        if($wallet){
            $prev_balance = $wallet[0]->current_balance;
            $current_balance = $amount + $prev_balance;
            try {
                $update_wallet = Utilities::switch_db('api')->select("UPDATE wallets set prev_balance = '$prev_balance', current_balance = '$current_balance' WHERE user_id = '$agency_id'");
            }catch(\Exception $e) {
                Utilities::switch_db('api')->rollback();
                return 'error';
            }

            $prev_bal = $wallet[0]->current_balance;
            $insert_history = [
                'id' => uniqid(),
                'user_id' => $agency_id,
                'amount' => $amount,
                'prev_balance' => $prev_bal,
                'current_balance' => $amount + $prev_bal,
                'status' => 1,
            ];
            try {
                $add_walletHistory = Utilities::switch_db('api')->table('walletHistories')->insert($insert_history);
            }catch (\Exception $e){
                Utilities::switch_db('api')->rollback();
                return 'error';
            }


        } else {
            $wallet =
                [
                    'id' => uniqid(),
                    'user_id' => $agency_id,
                    'prev_balance' => 0,
                    'current_balance' => $amount
                ];
            try {
                $insert_wallets = Utilities::switch_db('api')->table('wallets')->insert($wallet);
            }catch (\Exception $e) {
                Utilities::switch_db('api')->rollback();
                return 'error';
            }
            $prev_bal = 0;
            $insert_history = [
                'id' => uniqid(),
                'user_id' => $agency_id,
                'amount' => $amount,
                'prev_balance' => $prev_bal,
                'current_balance' => $amount + $prev_bal,
                'status' => 1,
            ];
            try {
                $add_walletHistory = Utilities::switch_db('api')->table('walletHistories')->insert($insert_history);
            }catch (\Exception $e) {
                Utilities::switch_db('api')->rollback();
                return 'error';
            }
        }

        Utilities::switch_db('api')->commit();
        return 'success';
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
        } else {
            return false;
        }
    }
}
