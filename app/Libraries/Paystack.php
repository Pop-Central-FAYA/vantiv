<?php

namespace Vanguard\Libraries;

use Ixudra\Curl\Facades\Curl;

class Paystack
{
    public static function query_api_transaction_verify($reference)
    {
        $result = array();
        $url = 'https://api.paystack.co/transaction/verify/'.$reference;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer '.env('PAYSTACK_SECRET_KEY').'']
        );
        $request = curl_exec($ch);
        curl_close($ch);

        if ($request) {
            $result = json_decode($request, true);
            dd($result);
            return $result;
        } else {
            return false;
        }
    }
}