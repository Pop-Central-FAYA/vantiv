<?php

namespace Vanguard\Http\Controllers\Agency;

use Illuminate\Http\Request;
use Monolog\Processor\UidProcessor;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Paystack;
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
        $agent_user_id = Utilities::switch_db('api')->select("SELECT user_id from agents where id = '$agency_id'");
        $user = $agent_user_id[0]->user_id;
        $user_det = Utilities::switch_db('api')->select("SELECT * from users where id = '$user'");

        $wallets = Utilities::switch_db('api')->select("SELECT SUM(current_balance) as balance from wallets where user_id = '$agency_id'");
        $wallet_histories = Utilities::switch_db('api')->select("SELECT * from walletHistories where user_id = '$agency_id'");
        $transactions = Utilities::switch_db('api')->select("SELECT * from transactions WHERE user_id = '$agency_id' ORDER BY time_created DESC ");

        return view('wallets.wallet_statement')->with('wallet', $wallets)->with('user_det', $user_det)->with('user_id', $agency_id)->with('history', $wallet_histories)
                                        ->with('transactions', $transactions)->with(['agency_id' => $agency_id, 'advertiser_id' => Session::get('advertiser_id')]);
    }

    public function getData(Datatables $datatables, Request $request)
    {
        $agency_id = \Session::get('agency_id');

        if($request->start_date && $request->stop_date){
            $start_date = $request->start_date;
            $stop_date = $request->stop_date;
            $transactions = Utilities::switch_db('api')->select("SELECT * from transactions where user_id = '$agency_id' 
                                                                    AND status != 'PENDING' AND time_created BETWEEN '$start_date' 
                                                                    AND '$stop_date' ORDER BY time_created desc");

            $j = 1;
            $all_transactions = [];

            foreach ($transactions as $transaction) {
                $all_transactions[] = [
                    'id' => $j,
                    'reference' => strtoupper($transaction->reference),
                    'type' => $transaction->type,
                    'amount' => $transaction->amount,
                    'date' => date('d/m/Y', strtotime($transaction->time_created)),
//                    'returning_balance' => $transaction
                ];
                $j++;
            }
            return $datatables->collection($all_transactions)
                ->editColumn('type', function ($all_transactions) {
                    if($all_transactions['type'] === 'FUND WALLET'){
                        return '<span class="span_state status_success">Credit</span>';
                    }else{
                        return '<span class="span_state status_danger">Debit</span>';
                    }
                })
                ->editColumn('amount', function ($all_transactions) {
                    if($all_transactions['type'] === 'FUND WALLET'){
                        return '<span class="span_state status_success">&#8358;'. number_format($all_transactions['amount'], 2) .'</span>';
                    }else{
                        return '<span class="span_state status_danger">&#8358;'. number_format($all_transactions['amount'], 2) .'</span>';
                    }
                })
                ->rawColumns(['type' => 'type', 'amount' => 'amount'])
                ->make(true);
        }

        $transactions = Utilities::switch_db('api')->select("SELECT * from transactions where user_id = '$agency_id' ORDER BY time_created desc");
        $j = 1;
        $all_transactions = [];

        foreach ($transactions as $transaction) {
            $all_transactions[] = [
                'id' => $j,
                'reference' => strtoupper($transaction->reference),
                'type' => $transaction->type,
                'amount' => $transaction->amount,
                'date' => date('d/m/Y', strtotime($transaction->time_created)),
//                'returning_balance' =>  $transaction
            ];
            $j++;
        }
        return $datatables->collection($all_transactions)
            ->editColumn('type', function ($all_transactions) {
                if($all_transactions['type'] === 'FUND WALLET'){
                    return '<span class="span_state status_success">Credit</span>';
                }else {
                    return '<span class="span_state status_danger">Debit</span>';
                }
            })
            ->editColumn('amount', function ($all_transactions) {
                if($all_transactions['type'] === 'FUND WALLET'){
                    return '<span class="span_state status_success">&#8358;'. number_format($all_transactions['amount'], 2) .'</span>';
                }else{
                    return '<span class="span_state status_danger">&#8358;'. number_format($all_transactions['amount'], 2) .'</span>';
                }
            })
            ->rawColumns(['type' => 'type', 'amount' => 'amount'])
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
        $agent_user_id = Utilities::switch_db('api')->select("SELECT user_id from agents where id = '$agency_id'");
        $user = $agent_user_id[0]->user_id;
        $user_det = Utilities::switch_db('api')->select("SELECT * from users where id = '$user'");

        $wallets = Utilities::switch_db('api')->select("SELECT SUM(current_balance) as balance from wallets where user_id = '$agency_id'");

        return view('wallets.create')->with('wallet', $wallets)->with('user_det', $user_det)->with('agency_id', $agency_id)->with('advertiser_id', Session::get('advertiser_id'))->with('user_id', $agency_id);
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

        $response = Paystack::query_api_transaction_verify($request->reference);

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
                $description = 'Wallet credited with '.$amount.' by '.$user_id;
                $user_activity = Api::saveActivity($user_id, $description);
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

        if($wallet){
            $previous_balance = $wallet[0]->current_balance;
            $current_balance = $amount + $previous_balance;
            $insert_history = Utilities::transactionHistory($agency_id, $amount, $current_balance, $previous_balance);
            $new_previous_balance = $insert_history['prev_balance'];
            $new_current_balance = $insert_history['current_balance'];
            try {
                Utilities::switch_db('api')->transaction(function() use($new_previous_balance, $new_current_balance, $agency_id, $insert_history) {
                    Utilities::switch_db('api')->select("UPDATE wallets set prev_balance = '$new_previous_balance', current_balance = '$new_current_balance' WHERE user_id = '$agency_id'");
                    Utilities::switch_db('api')->table('walletHistories')->insert($insert_history);
                });
            }catch(\Exception $e){
                return 'error';
            }


        } else {
            $wallet = [
                    'id' => uniqid(),
                    'user_id' => $agency_id,
                    'prev_balance' => 0,
                    'current_balance' => $amount
                ];
            $insert_history = Utilities::transactionHistory($agency_id, $amount, $amount, 0);
            try {
                Utilities::switch_db('api')->transaction(function () use ($insert_history, $wallet) {
                    Utilities::switch_db('api')->table('wallets')->insert($wallet);
                    Utilities::switch_db('api')->table('walletHistories')->insert($insert_history);
                });
            }catch (\Exception $e){
                return 'error';
            }
        }

        return 'success';
    }



}
