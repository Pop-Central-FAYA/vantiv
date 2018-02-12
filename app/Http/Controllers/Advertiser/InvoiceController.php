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

        $all_invoices = Utilities::switch_db('reports')->select("SELECT * FROM invoices WHERE  agency_id = '$advertiser_id'");

        $invoice_campaign_details = [];

        foreach ($all_invoices as $invoice) {

            $campaign_id = $invoice->campaign_id;

            $campaign = Utilities::switch_db('reports')->select("SELECT * FROM campaigns WHERE id = '$campaign_id'");
            $brand_id = $campaign[0]->brand;
            $brand_name = Utilities::switch_db('api')->select("SELECT name from brands where id = '$brand_id'");

            $invoice_campaign_details[] = [
                'invoice_number' => $invoice->invoice_number,
                'actual_amount_paid' => $invoice->actual_amount_paid,
                'refunded_amount' => $invoice->refunded_amount,
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

        $all_invoices = Utilities::switch_db('reports')->select("SELECT * FROM invoices WHERE  agency_id = '$advertiser_id' AND status = 0");

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

        return view('advertisers.invoices.pending')
            ->with('pending_invoices', $invoice_campaign_details);
    }
}