<?php

namespace Vanguard\Http\Controllers\Agency;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\Utilities;

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
        $wallets = Utilities::switch_db('api')->select("SELECT SUM(current_balance) as balance from wallets where user_id = '$agency_id'");
        $wallet_history = Utilities::switch_db('api')->select("SELECT * from walletHistories where user_id = '$agency_id'");
        return view('agency.wallets.wallet_statement')->with('wallet', $wallets)->with('history', $wallet_history);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $agency_id = \Session::get('agency_id');
        $wallets = Utilities::switch_db('api')->select("SELECT SUM(current_balance) as balance from wallets where user_id = '$agency_id'");
        return view('agency.wallets.create')->with('wallet', $wallets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAmount(Request $request)
    {
        $this->validate($request, [
           'amount' => 'required',
        ]);

        $getAmount = $request->amount;

        session(['amount' => $getAmount]);

        return redirect()->route('amount.pay');
    }

    public function getPay()
    {
        $pay = \Session::get('amount');
        return view('agency.wallets.pay_form')->with('amount', $pay);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
