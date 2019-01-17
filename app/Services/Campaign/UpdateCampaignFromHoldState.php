<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Utilities;

class UpdateCampaignFromHoldState
{
    protected $payment_method;
    protected $campaign_id;
    protected $broadcaster_id;
    protected $agency_id;

    public function __construct($campaign_id, $payment_method, $broadcaster_id, $agency_id)
    {
        $this->campaign_id = $campaign_id;
        $this->payment_method = $payment_method;
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
    }

    public function updateCampaignInformation()
    {
        $campaign = new SingleCampaign($this->campaign_id, $this->broadcaster_id, $this->agency_id);
        $campaign = $campaign->getSingleCampaign();
        $payment_id = $campaign->payment_id;
        $invoice_id = $campaign->invoice_id;
        try{
            Utilities::switch_db('api')->transaction(function () use($payment_id, $invoice_id) {
                $update_campaign_details = new UpdateCampaignDetails($this->campaign_id);
                $update_campaign_details->updateCampaignStatus();
                $update_payment_details = new UpdatePaymentDetails($payment_id, $this->payment_method);
                $update_payment_details->updatePaymentDetails();
                $update_invoice_details = new UpdateInvoiceDetails($invoice_id);
                $update_invoice_details->updateInvoiceDetails();
            });
        }catch (\Exception $exception){
            return 'error';
        }
        return 'success';
    }
}
