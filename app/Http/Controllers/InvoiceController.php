<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;

class InvoiceController extends Controller
{
    public function all()
    {
        $user_id = \Auth::user()->id;

        $user_walkin = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE user_id = '$user_id' LIMIT 1");

        $walkin_id = $user_walkin[0]->id;

        $all_invoices = Utilities::switch_db('reports')->select("SELECT * FROM invoices WHERE walkins_id = '$walkin_id'");

        $invoice_campaign_details = [];

        foreach ($all_invoices as $invoice) {

            $campaign_id = $invoice->campaign_id;

            $campaigns = Utilities::switch_db('reports')->select("SELECT * FROM campaigns WHERE walkins_id = '$campaign_id'");

            $invoice_campaign_details[] = [
                'invoice_number' => $invoice->invoice_number,
                'actual_amount_paid' => $invoice->actual_amount_paid,
                'refunded_amount' => $invoice->refunded_amount,
                'campaign_brand' => $campaigns->brand,
                'campaign_name' => $campaigns->name
            ];
        }

        return view('invoices.all-invoices')
            ->with('all_invoices', $invoice_campaign_details);
    }


    public function pending()
    {
        $user_id = \Auth::user()->id;

        $user_walkin = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE user_id = '$user_id' LIMIT 1");

        $walkin_id = $user_walkin[0]->id;

        $all_invoices = Utilities::switch_db('reports')->select("SELECT * FROM invoices WHERE walkins_id = '$walkin_id' AND status = 0");

        $invoice_campaign_details = [];

        foreach ($all_invoices as $invoice) {

            $campaign_id = $invoice->campaign_id;

            $campaigns = Utilities::switch_db('reports')->select("SELECT * FROM campaigns WHERE walkins_id = '$campaign_id'");

            $invoice_campaign_details[] = [
                'invoice_number' => $invoice->invoice_number,
                'actual_amount_paid' => $invoice->actual_amount_paid,
                'refunded_amount' => $invoice->refunded_amount,
                'campaign_brand' => $campaigns->brand,
                'campaign_name' => $campaigns->name
            ];
        }

        return view('invoices.all-invoices')
            ->with('pending_invoices', $invoice_campaign_details);
    }


}
