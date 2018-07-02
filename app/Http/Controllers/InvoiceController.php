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

        $all_invoices = Utilities::switch_db('reports')->select("SELECT * FROM invoiceDetails WHERE  agency_id = '$agency_id' GROUP BY invoice_id ORDER BY time_created DESC");

        $invoice_campaign_details = [];

        foreach ($all_invoices as $invoice) {

//            $campaign_id = $invoice->campaign_id;

            $campaign_list = Utilities::switch_db('api')->select("SELECT * from invoices where id = '$invoice->invoice_id' ");

            $campaign_id = $campaign_list[0]->campaign_id;

            $user_id = $invoice->user_id;

            $campaign = Utilities::switch_db('reports')->select("SELECT * FROM campaignDetails WHERE campaign_id = '$campaign_id' GROUP BY campaign_id");
            $brand_id = $campaign[0]->brand;
            $brand_name = Utilities::switch_db('api')->select("SELECT name from brands where id = '$brand_id'");
            $user_details = $user_details = Utilities::switch_db('api')->select("SELECT * FROM users WHERE id = '$user_id'");

            $payment = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$campaign_id'");

            $invoice_campaign_details[] = [
                'id' => $invoice->invoice_id,
                'invoice_number' => $invoice->invoice_number,
                'actual_amount_paid' => number_format($payment[0]->total, 2),
                'refunded_amount' => $invoice->refunded_amount,
                'name' => $user_details && $user_details[0] ? $user_details[0]->lastname . ' ' . $user_details[0]->firstname : '',
                'status' => $invoice->status,
                'campaign_brand' => $brand_name[0]->name,
                'campaign_name' => $campaign[0]->name,
                'start_date' => date('Y/m/d', strtotime($campaign[0]->start_date)),
                'end_date' =>   date('Y/m/d', strtotime($campaign[0]->stop_date)),
            ];
        }

        return view('invoices.all-invoices')
            ->with('all_invoices', $invoice_campaign_details);
    }


    public function pending()
    {
        $agency_id = Session::get('agency_id');

        $all_invoices = Utilities::switch_db('reports')->select("SELECT * FROM invoiceDetails WHERE  agency_id = '$agency_id' AND status = 0 GROUP BY invoice_id");

        $invoice_campaign_details = [];

        foreach ($all_invoices as $invoice) {

//            $campaign_id = $invoice->campaign_id;

            $campaign_list = Utilities::switch_db('api')->select("SELECT * from invoices where id = '$invoice->invoice_id' ");

            $campaign_id = $campaign_list[0]->campaign_id;

            $campaign = Utilities::switch_db('reports')->select("SELECT * FROM campaignDetails WHERE campaign_id = '$campaign_id'");
            $brand_id = $campaign[0]->brand;
            $brand_name = Utilities::switch_db('api')->select("SELECT name from brands where id = '$brand_id'");

            $payment = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$campaign_id'");

            $invoice_campaign_details[] = [
                'id' => $invoice->invoice_id,
                'invoice_number' => $invoice->invoice_number,
                'actual_amount_paid' => $payment[0]->total,
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
        $agency_id = Session::get('agency_id');

        $invoice = Utilities::switch_db('reports')->select("SELECT SUM(actual_amount_paid) as actual_amount_paid, invoice_number, invoice_id FROM invoiceDetails WHERE invoice_id = '$invoice_id' LIMIT 1");
        $amount = $invoice[0]->actual_amount_paid;

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
            'reference' => $invoice[0]->invoice_id,
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

            $update_invoice = Utilities::switch_db('reports')->select("UPDATE invoiceDetails SET status = 1 WHERE invoice_id = '$invoice_id'");

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