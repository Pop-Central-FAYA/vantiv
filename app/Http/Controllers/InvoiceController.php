<?php

namespace Vanguard\Http\Controllers;

use Session;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Utilities;

class InvoiceController extends Controller
{
    public function all()
    {
        $agency_id = Session::get('agency_id');

        $all_invoices = Utilities::switch_db('reports')->select("SELECT * FROM invoices WHERE  agency_id = '$agency_id'");

        $invoice_campaign_details = [];

        foreach ($all_invoices as $invoice) {

            $campaign_id = $invoice->campaign_id;
            $user_id = $invoice->user_id;

            $campaign = Utilities::switch_db('reports')->select("SELECT * FROM campaigns WHERE id = '$campaign_id'");
            $brand_id = $campaign[0]->brand;
            $brand_name = Utilities::switch_db('api')->select("SELECT name from brands where id = '$brand_id'");
            $user_details = $user_details = \DB::select("SELECT * FROM users WHERE id = '$user_id'");

            $invoice_campaign_details[] = [
                'invoice_number' => $invoice->invoice_number,
                'actual_amount_paid' => $invoice->actual_amount_paid,
                'refunded_amount' => $invoice->refunded_amount,
                'name' => $user_details && $user_details[0] ? $user_details[0]->last_name . ' ' . $user_details[0]->first_name : '',
                'status' => $invoice->status,
                'campaign_brand' => $brand_name[0]->name,
                'campaign_name' => $campaign[0]->name
            ];
        }

        return view('invoices.all-invoices')
            ->with('all_invoices', $invoice_campaign_details);
    }


    public function pending()
    {
        $agency_id = Session::get('agency_id');

        $all_invoices = Utilities::switch_db('reports')->select("SELECT * FROM invoices WHERE  agency_id = '$agency_id' AND status = 0");

        $invoice_campaign_details = [];

        foreach ($all_invoices as $invoice) {

            $campaign_id = $invoice->campaign_id;

            $campaign = Utilities::switch_db('reports')->select("SELECT * FROM campaigns WHERE id = '$campaign_id'");
            $brand_id = $campaign[0]->brand;
            $brand_name = Utilities::switch_db('api')->select("SELECT name from brands where id = '$brand_id'");

            $invoice_campaign_details[] = [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'actual_amount_paid' => $invoice->actual_amount_paid,
                'refunded_amount' => $invoice->refunded_amount,
                'status' => $invoice->status,
                'campaign_brand' => $brand_name[0]->name,
                'campaign_name' => $campaign[0]->name
            ];
        }

        return view('invoices.pending-invoices')
            ->with('pending_invoices', $invoice_campaign_details);
    }

    public function approveInvoice($invoice_id)
    {
        $invoice = Utilities::switch_db('reports')->select("SELECT * FROM invoices WHERE id = '$invoice_id' LIMIT 1");
        $amount = $invoice[0]->actual_amount_paid;
        $agency_id = Session::get('agency_id');

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $description = 'Invoice with invoice number '.$invoice[0]->invoice_number.' has been approved by '.$agency_id.'';
        $ip = request()->ip();

        $wallet = Utilities::switch_db('reports')->select("SELECT * FROM wallets WHERE user_id = '$agency_id'");
        $current_balance = $wallet[0]->current_balance;
        $new_balance = $current_balance - $amount;

        if ($current_balance < $amount) {
            return redirect()->back()->with('error', 'Insufficient Balance in Wallet');
        }

        $transaction = Utilities::switch_db('reports')->table('transactions')->insert([
            'id' => uniqid(),
            'amount' => $amount,
            'user_id' => $agency_id,
            'reference' => $invoice[0]->id,
            'ip_address' => request()->ip(),
            'type' => 'DEBIT WALLET',
            'message' => 'Debit successful'
        ]);

        $walletHistory = Utilities::switch_db('reports')->table('walletHistories')->insert([
            'id' => uniqid(),
            'user_id' => $agency_id,
            'amount' => $amount,
            'prev_balance' => $current_balance,
            'status' => 1,
            'current_balance' => $new_balance
        ]);

        $updateWallet = Utilities::switch_db('reports')->select("UPDATE wallets SET current_balance = '$new_balance', prev_balance = '$current_balance' WHERE user_id = '$agency_id'");

        if ($transaction && $walletHistory && empty($updateWallet)) {

            $update_invoice = Utilities::switch_db('reports')->select("UPDATE invoices SET status = 1 WHERE id = '$invoice_id'");

            if (empty($update_invoice)) {
                $save_activity = Api::saveActivity($agency_id, $description, $ip, $user_agent);
                return redirect()->back()->with('success', 'Invoice Approved Successfully');
            } else {
                return redirect()->back()->with('error', 'Invoice not Approved Successfully');
            }
        } else {
            return redirect()->back()->with('error', 'Error Approving Invoice');
        }

    }


}