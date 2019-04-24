<?php

namespace Vanguard\Services\Invoice;

use Riskihajar\Terbilang\Terbilang;
use Vanguard\Services\Traits\InvoiceQueryTrait;

class InvoiceDetails
{
    protected $invoice_id;
    use InvoiceQueryTrait;

    public function __construct($invoice_id)
    {
        $this->invoice_id = $invoice_id;
    }

    public function getInvoiceDetails()
    {
        return $this->invoiceBaseQuery()
                    ->join('companies', 'companies.id', '=', 'invoiceDetails.broadcaster_id')
                    ->leftJoin('discounts_old', function ($query) {
                        return $query->on('discounts_old.discount_type_value', '=', 'invoiceDetails.agency_id')
                                    ->on('discounts_old.broadcaster', '=', 'invoiceDetails.broadcaster_id');
                    })
                    ->join('selected_adslots', 'selected_adslots.campaign_id', '=', 'campaignDetails.campaign_id')
                    ->addSelect('campaignDetails.start_date', 'campaignDetails.stop_date', 'walkIns.company_logo AS client_logo',
                        'invoiceDetails.broadcaster_id', 'invoiceDetails.actual_amount_paid AS campaign_cost',
                        'campaignDetails.adslots AS quantity', 'companies.name AS company_name', 'walkIns.company_name AS client_name',
                        'walkIns.location AS client_address')
                    ->selectRaw("COALESCE(discounts_old.percent_value, 0) AS agency_discount_percentage")
                    ->where('invoiceDetails.invoice_id', $this->invoice_id)
                    ->groupBy('invoiceDetails.broadcaster_id')
                    ->get();
    }

    public function invoiceDetailsData()
    {
        $invoice_details = $this->getInvoiceDetails();
        return [
            'invoice_id' => $invoice_details[0]->invoice_id,
            'client_name' => $invoice_details[0]->client_name,
            'client_address' => $invoice_details[0]->client_address,
            'client_logo' => $invoice_details[0]->client_logo,
            'campaign_name' => $invoice_details[0]->campaign_name,
            'start_date' => $invoice_details[0]->start_date,
            'end_date' => $invoice_details[0]->stop_date,
            'invoice_number' => $invoice_details[0]->invoice_number,
            'invoice_date' => $invoice_details[0]->date,
            'publishers_specific_details' => $this->getInvoiceTableData($invoice_details),
            'total_gross' => number_format($this->sumGross($invoice_details), 2),
            'total_value_amount' => number_format($this->sumValueAmount($invoice_details), 2),
            'agency_commission_value' => number_format($this->sumAgencyTotal($invoice_details), 2),
            'total_vat' => number_format($this->sumVat($invoice_details), 2),
            'total_net' => $this->sumNetTotal($invoice_details),
            'total_net_word' => $this->formatNetTotalToWord($this->sumNetTotal($invoice_details))
        ];
    }

    public function getInvoiceTableData($invoice_details)
    {
        $table_data = [];
        foreach ($invoice_details as $invoice_detail){
            $agency_discount_value = $invoice_detail->agency_discount_percentage == 0 ? 0 : round(($invoice_detail->agency_discount_percentage / 100) * $invoice_detail->campaign_cost);
            $agency_commission_value = round((15 / 100) * $invoice_detail->campaign_cost);
            $table_data[] = [
                'publisher' => $invoice_detail->company_name,
                'quantity' => $invoice_detail->quantity,
                'rate' => number_format(round($invoice_detail->campaign_cost / $invoice_detail->quantity),2),
                'gross' => number_format($invoice_detail->campaign_cost, 2),
                'agency_percentage_discount' => $invoice_detail->agency_discount_percentage,
                'agency_discount_value' => number_format($agency_discount_value, 2),
                'agency_commission_percentage' => 15,
                'agency_commission_value' => number_format($agency_commission_value, 2),
                'others' => 0.00,
                'vat' => number_format(round((5/100)*$invoice_detail->campaign_cost), 2),
                'total' => number_format($this->calculateNetTotal($invoice_detail->campaign_cost, $agency_discount_value, $agency_commission_value),2)
            ];
        }
        return $table_data;
    }

    public function calculateNetTotal($campaign_cost, $discount_value, $commission_value)
    {
        return $campaign_cost - $discount_value - $commission_value;
    }

    public function sumGross($invoice_details)
    {
        $summed_gross = 0;
        foreach ($invoice_details as $invoice_detail){
            $summed_gross += $invoice_detail->campaign_cost;
        }
        return $summed_gross;
    }

    public function sumValueAmount($invoice_details)
    {
        $summed_value_amount = 0;
        foreach ($invoice_details as $invoice_detail){
            $agency_discount_value = $invoice_detail->agency_discount_percentage == 0 ? 0 : round(($invoice_detail->agency_discount_percentage / 100) * $invoice_detail->campaign_cost);
            $summed_value_amount += $agency_discount_value;
        }
        return $summed_value_amount;
    }

    public function sumAgencyTotal($invoice_details)
    {
        $sum_agency_total = 0;
        foreach ($invoice_details as $invoice_detail){
            $agency_commission_value = round((15 / 100) * $invoice_detail->campaign_cost);
            $sum_agency_total += $agency_commission_value;
        }
        return $sum_agency_total;
    }

    public function sumVat($invoice_details)
    {
        $summed_vat = 0;
        foreach ($invoice_details as $invoice_detail){
            $vat_value = round((5/100)*$invoice_detail->campaign_cost);
            $summed_vat += $vat_value;
        }
        return $summed_vat;
    }

    public function sumNetTotal($invoice_details)
    {
        $summed_net_total = 0;
        foreach ($invoice_details as $invoice_detail){
            $agency_discount_value = $invoice_detail->agency_discount_percentage == 0 ? 0 : round(($invoice_detail->agency_discount_percentage / 100) * $invoice_detail->campaign_cost);
            $agency_commission_value = round((15 / 100) * $invoice_detail->campaign_cost);
            $get_net_total = $this->calculateNetTotal($invoice_detail->campaign_cost, $agency_discount_value, $agency_commission_value);
            $summed_net_total += $get_net_total;
        }
        return $summed_net_total;
    }

    public function formatNetTotalToWord($net_total)
    {
        return \Riskihajar\Terbilang\Facades\Terbilang::make($net_total, ' Naira Only');
    }

}
