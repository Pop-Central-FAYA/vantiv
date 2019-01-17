<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Enum\CampaignStatus;
use Vanguard\Libraries\Paystack;
use Vanguard\Libraries\Utilities;
use Vanguard\Models\Transaction;

class CampaignCardPayment
{
    protected $user_id;
    protected $transaction_reference;
    protected $total_amount;
    protected $campaign_id;
    protected $payment_method;
    protected $broadcaster_id;
    protected $agency_id;

    public function __construct($user_id, $transaction_reference, $total_amount, $campaign_id, $payment_method, $broadcaster_id, $agency_id)
    {
        $this->user_id = $user_id;
        $this->transaction_reference = $transaction_reference;
        $this->total_amount = $total_amount;
        $this->campaign_id = $campaign_id;
        $this->payment_method = $payment_method;
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
    }

    public function processCampaignWithPaystack()
    {
        $this->createTransaction();
        $payment_response = $this->processPaymentWithPaystack();
        if($payment_response['status'] == true){
            try{
                $amount = ($payment_response['data']['amount']/100);
                $card_type = $payment_response['data']['authorization']['card_type'];
                $transaction_message = $payment_response['message'];
                $reference = $payment_response['data']['reference'];
                $ip_address = $payment_response['data']['ip_address'];
                $fee = $payment_response['data']['fees'];
                $transaction_type = CampaignStatus::TRANSACTION_TYPE;
                Utilities::switch_db('api')->transaction(function () use($card_type, $ip_address, $fee, $transaction_type, $transaction_message, $reference) {
                    $this->updateTransaction($card_type, $ip_address, $fee, $transaction_type, $transaction_message, $reference);
                    $update_campaign_from_hold = new UpdateCampaignFromHoldState($this->campaign_id, $this->payment_method, $this->broadcaster_id, $this->agency_id);
                    $update_campaign_from_hold->updateCampaignInformation();
                });
            }catch(\Exception $exception){
                return 'error';
            }
            return 'success';
        }else{
            return 'error';
        }
    }

    public function createTransaction()
    {
        $transaction = new Transaction();
        $transaction->id = uniqid();
        $transaction->user_id = $this->user_id;
        $transaction->reference = $this->transaction_reference;
        $transaction->amount = $this->total_amount;
        $transaction->status = CampaignStatus::PAYMENT_PENDING;
        $transaction->save();
        return $transaction;
    }

    public function processPaymentWithPaystack()
    {
        return Paystack::query_api_transaction_verify($this->transaction_reference);
    }

    public function updateTransaction($card_type, $ip_address, $fees, $transaction_type, $transaction_message, $reference)
    {
        $transaction = Transaction::where('reference', $reference)->first();
        $transaction->card_type = $card_type;
        $transaction->status = CampaignStatus::PAYMENT_SUCCESS;
        $transaction->ip_address = $ip_address;
        $transaction->fees = $fees;
        $transaction->type = $transaction_type;
        $transaction->message = $transaction_message;
        $transaction->save();
    }
}
