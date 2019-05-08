<?php

namespace Vanguard\Services\RateCard;

use Vanguard\Libraries\Enum\ProgramStatus;
use Vanguard\Models\Company;
use Vanguard\Models\Ratecard\Ratecard;
use Vanguard\Models\RateCardDuration;

class StoreBaseRateCard
{
    protected $request_data;

    public function __construct($request_data)
    {
        $this->request_data = $request_data;
    }

    private function storeRateCard()
    {
        $rate_card = new Ratecard();
        $rate_card->title = $this->request_data['name'];
        $rate_card->company_id = $this->request_data['company_id'];
        $rate_card->slug = str_slug($this->request_data['name']);
        $rate_card->status = ProgramStatus::ACTIVE;
        $rate_card->is_base = $this->request_data['is_base'];
        $rate_card->save();
        return $rate_card;
    }

    public function run()
    {
        \DB::transaction(function () use (&$rate_card) {
            self::toggleRatecard($this->request_data['company_id'], $this->request_data['is_base']);
            $rate_card = $this->storeRateCard();
            for ($i = 0; $i < count($this->request_data['duration']); $i++){
                $rate_card_duration = new RateCardDuration();
                $rate_card_duration->rate_card_id = $rate_card->id;
                $rate_card_duration->duration = $this->request_data['duration'][$i];
                $rate_card_duration->price = $this->request_data['price'][$i];
                $rate_card_duration->save();
            }
        });
        return ['status' => 'success', 'rate_card' => $rate_card];
    }

    public function toggleRatecard($company_id, $is_base)
    {
        $company = Company::find($company_id);
        if($is_base){
            $company->rate_cards()->update([
                'is_base' => false
            ]);
        }

    }
}
