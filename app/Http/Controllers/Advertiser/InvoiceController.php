<?php

namespace Vanguard\Http\Controllers\Advertiser;

use Session;
use Vanguard\Libraries\Utilities;
use Vanguard\Http\Controllers\Controller;

class InvoiceController extends Controller
{
    public function all()
    {
        $advertiser_id = Session::get('advertiser_id');

        $all_invoices = Utilities::switch_db('reports')->select("SELECT * FROM invoiceDetails WHERE  agency_id = '$advertiser_id' GROUP BY invoice_id");

        $invoice_campaign_details = [];

        foreach ($all_invoices as $invoice) {

//            $campaign_id = $invoice->campaign_id;

            $campaign_list = Utilities::switch_db('api')->select("SELECT * from invoices where id = '$invoice->invoice_id' ");

            $campaign_id = $campaign_list[0]->campaign_id;

            $user_id = $invoice->user_id;

            $campaign = Utilities::switch_db('reports')->select("SELECT * FROM campaignDetails WHERE campaign_id = '$campaign_id' GROUP BY campaign_id");
            $brand_id = $campaign[0]->brand;
            $brand_name = Utilities::switch_db('api')->select("SELECT name from brands where id = '$brand_id'");
            $user_details = $user_details = \DB::select("SELECT * FROM users WHERE id = '$user_id'");

            $payment = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$campaign_id'");

            $invoice_campaign_details[] = [
                'invoice_number' => $invoice->invoice_number,
                'actual_amount_paid' => number_format($payment[0]->total, 2),
                'refunded_amount' => $invoice->refunded_amount,
                'name' => $user_details && $user_details[0] ? $user_details[0]->last_name . ' ' . $user_details[0]->first_name : '',
                'status' => $invoice->status,
                'campaign_brand' => $brand_name[0]->name,
                'campaign_name' => $campaign[0]->name
            ];
        }

        return view('advertisers.invoices.all')
            ->with('all_invoices', $invoice_campaign_details);
    }

    public function pending()
    {
        $advertiser_id = Session::get('advertiser_id');

        $all_invoices = Utilities::switch_db('reports')->select("SELECT * FROM invoiceDetails WHERE  agency_id = '$advertiser_id' AND status = 0 GROUP BY invoice_id");

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

        return view('advertisers.invoices.pending')
            ->with('pending_invoices', $invoice_campaign_details);
    }
}