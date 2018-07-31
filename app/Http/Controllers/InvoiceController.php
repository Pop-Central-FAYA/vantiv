<?php

namespace Vanguard\Http\Controllers;

use Session;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Utilities;
use Yajra\DataTables\DataTables;

class InvoiceController extends Controller
{
    public function all()
    {
        $agency_id = Session::get('agency_id');

        $all_invoices = Utilities::switch_db('reports')->select("SELECT i_d.user_id, i_d.invoice_id, i_d.invoice_number, i_d.refunded_amount, i_d.status, i_d.time_created, u.firstname, u.lastname, i.campaign_id FROM invoiceDetails as i_d, invoices as i, users as u WHERE  i_d.agency_id = '$agency_id' and i_d.invoice_id = i.id and u.id = i_d.user_id GROUP BY i_d.invoice_id ORDER BY i_d.time_created DESC");

        $invoice_campaign_details = [];

        foreach ($all_invoices as $invoice) {

            $campaign_details = Utilities::switch_db('api')->select("SELECT c.name as campaign_name, b.name as brand_name, p.total from campaignDetails as c, payments as p, brands as b where c.campaign_id = '$invoice->campaign_id' and p.campaign_id = '$invoice->campaign_id' and b.id = c.brand GROUP BY c.campaign_id");

            $invoice_campaign_details[] = [
                'id' => $invoice->invoice_id,
                'invoice_number' => $invoice->invoice_number,
                'actual_amount_paid' => number_format($campaign_details[0]->total, 2),
                'refunded_amount' => $invoice->refunded_amount,
                'name' => $invoice->lastname . ' ' . $invoice->firstname,
                'status' => $invoice->status,
                'campaign_brand' => $campaign_details[0]->brand_name,
                'campaign_name' => $campaign_details[0]->campaign_name,
                'date' => date('Y/m/d', strtotime($invoice->time_created)),
            ];
        }

        return view('invoices.all-invoices')
            ->with('all_invoices', $invoice_campaign_details);
    }

    public function getInvoiceDate(DataTables $dataTables)
    {
        $agency_id = Session::get('agency_id');

        $all_invoices = Utilities::switch_db('reports')->select("SELECT i_d.user_id, i_d.invoice_id, i_d.invoice_number, i_d.refunded_amount, i_d.status, i_d.time_created, u.firstname, u.lastname, i.campaign_id FROM invoiceDetails as i_d, invoices as i, users as u WHERE  i_d.agency_id = '$agency_id' and i_d.invoice_id = i.id and u.id = i_d.user_id GROUP BY i_d.invoice_id ORDER BY i_d.time_created DESC");

        $invoice_campaign_details = [];
        $j = 1;

        foreach ($all_invoices as $invoice) {

            $campaign_details = Utilities::switch_db('api')->select("SELECT c.name as campaign_name, b.name as brand_name, p.total from campaignDetails as c, payments as p, brands as b where c.campaign_id = '$invoice->campaign_id' and p.campaign_id = '$invoice->campaign_id' and b.id = c.brand GROUP BY c.campaign_id");

            $invoice_campaign_details[] = [
                's_n' => $j,
                'id' => $invoice->invoice_id,
                'invoice_number' => $invoice->invoice_number,
                'actual_amount_paid' => number_format($campaign_details[0]->total, 2),
                'refunded_amount' => $invoice->refunded_amount,
                'name' => $invoice->lastname . ' ' . $invoice->firstname,
                'status' => $invoice->status,
                'campaign_brand' => $campaign_details[0]->brand_name,
                'campaign_name' => $campaign_details[0]->campaign_name,
                'date' => date('Y/m/d', strtotime($invoice->time_created)),
            ];
            $j++;
        }

        return $dataTables->collection($invoice_campaign_details)
            ->addColumn('status', function ($invoice_campaign_details){
                if($invoice_campaign_details['status'] === 1){
                    return '<span class="span_state status_success">Approved</span></td>';
                }else{
                    return '<a href="#approve_invoice'.$invoice_campaign_details['id'].'" class="span_state status_pending modal_invoice_click">Pending</a>';
                }
            })->addColumn('view', function ($invoice_campaign_details){
                return '<a href="#invoice'.$invoice_campaign_details['id'].'" class="modal_view_invoice_click">View</a>';
            })
            ->rawColumns(['status' => 'status', 'view' => 'view'])
            ->addIndexColumn()
            ->make(true);
    }


    public function pending()
    {
        $agency_id = Session::get('agency_id');

        $all_pending_invoices = Utilities::switch_db('reports')->select("SELECT i_d.user_id, i_d.invoice_id, i_d.invoice_number, i_d.refunded_amount, i_d.status, i_d.time_created, u.firstname, u.lastname, i.campaign_id FROM invoiceDetails as i_d, invoices as i, users as u WHERE  i_d.agency_id = '$agency_id' and i_d.invoice_id = i.id and u.id = i_d.user_id and i_d.status = 0 GROUP BY i_d.invoice_id ORDER BY i_d.time_created DESC");

        $invoice_campaign_details = [];

        foreach ($all_pending_invoices as $invoice) {

            $campaign_details = Utilities::switch_db('api')->select("SELECT c.name as campaign_name, b.name as brand_name, p.total from campaignDetails as c, payments as p, brands as b where c.campaign_id = '$invoice->campaign_id' and p.campaign_id = '$invoice->campaign_id' and b.id = c.brand GROUP BY c.campaign_id");

            $invoice_campaign_details[] = [
                'id' => $invoice->invoice_id,
                'invoice_number' => $invoice->invoice_number,
                'actual_amount_paid' => number_format($campaign_details[0]->total, 2),
                'refunded_amount' => $invoice->refunded_amount,
                'name' => $invoice->lastname . ' ' . $invoice->firstname,
                'status' => $invoice->status,
                'campaign_brand' => $campaign_details[0]->brand_name,
                'campaign_name' => $campaign_details[0]->campaign_name,
                'date' => date('Y/m/d', strtotime($invoice->time_created)),
            ];
        }


        return view('invoices.pending-invoices')
            ->with('all_invoices', $invoice_campaign_details);
    }

    public function pendingData(DataTables $dataTables)
    {
        $agency_id = Session::get('agency_id');

        $all_pending_invoices = Utilities::switch_db('reports')->select("SELECT i_d.user_id, i_d.invoice_id, i_d.invoice_number, i_d.refunded_amount, i_d.status, i_d.time_created, u.firstname, u.lastname, i.campaign_id FROM invoiceDetails as i_d, invoices as i, users as u WHERE  i_d.agency_id = '$agency_id' and i_d.invoice_id = i.id and u.id = i_d.user_id and i_d.status = 0 GROUP BY i_d.invoice_id ORDER BY i_d.time_created DESC");

        $invoice_campaign_details = [];
        $j = 1;

        foreach ($all_pending_invoices as $invoice) {

            $campaign_details = Utilities::switch_db('api')->select("SELECT c.name as campaign_name, b.name as brand_name, p.total from campaignDetails as c, payments as p, brands as b where c.campaign_id = '$invoice->campaign_id' and p.campaign_id = '$invoice->campaign_id' and b.id = c.brand GROUP BY c.campaign_id");

            $invoice_campaign_details[] = [
                's_n' => $j,
                'id' => $invoice->invoice_id,
                'invoice_number' => $invoice->invoice_number,
                'actual_amount_paid' => number_format($campaign_details[0]->total, 2),
                'refunded_amount' => $invoice->refunded_amount,
                'name' => $invoice->lastname . ' ' . $invoice->firstname,
                'status' => $invoice->status,
                'campaign_brand' => $campaign_details[0]->brand_name,
                'campaign_name' => $campaign_details[0]->campaign_name,
                'date' => date('Y/m/d', strtotime($invoice->time_created)),
            ];
            $j++;
        }

        return $dataTables->collection($invoice_campaign_details)
            ->addColumn('status', function ($invoice_campaign_details){
                if($invoice_campaign_details['status'] === 1){
                    return '<span class="span_state status_success">Approved</span></td>';
                }else{
                    return '<a href="#approve_invoice'.$invoice_campaign_details['id'].'" class="span_state status_pending modal_invoice_click">Pending</a>';
                }
            })->addColumn('view', function ($invoice_campaign_details){
                return '<a href="#invoice'.$invoice_campaign_details['id'].'" class="modal_view_invoice_click">View</a>';
            })
            ->rawColumns(['status' => 'status', 'view' => 'view'])
            ->addIndexColumn()
            ->make(true);
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