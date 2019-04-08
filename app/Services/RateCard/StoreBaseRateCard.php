<?php

namespace Vanguard\Services\RateCard;

use Vanguard\Libraries\Enum\ProgramStatus;
use Vanguard\Models\Ratecard\Ratecard;
use Vanguard\Models\RateCardDuration;

class StoreBaseRateCard
{
    protected $company_id;
    protected $duration;
    protected $price;
    protected $title;

    public function __construct($company_id, $duration, $price, $title)
    {
        $this->company_id = $company_id;
        $this->duration = $duration;
        $this->price = $price;
        $this->title = $title;
    }

    private function storeBaseRateCard()
    {
        $rate_card = new Ratecard();
        $rate_card->title = $this->title;
        $rate_card->company_id = $this->company_id;
        $rate_card->slug = str_slug($this->title);
        $rate_card->status = ProgramStatus::ACTIVE;
        $rate_card->save();
        return $rate_card;
    }

    public function storeRateCardWithDurationAndPrice()
    {
        \DB::transaction(function () use (&$rate_card) {
            $rate_card = $this->storeBaseRateCard();
            for ($i = 0; $i < count($this->duration); $i++){
                $rate_card_duration = new RateCardDuration();
                $rate_card_duration->rate_card_id = $rate_card->id;
                $rate_card_duration->duration = $this->duration[$i];
                $rate_card_duration->price = $this->price[$i];
                $rate_card_duration->save();
            }
        });
        return ['status' => 'success', 'rate_card' => $rate_card];
    }
}
