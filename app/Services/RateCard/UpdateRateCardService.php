<?php

namespace Vanguard\Services\RateCard;


use Vanguard\Models\RateCardDuration;

class UpdateRateCardService
{
    protected $rate_card_id;
    protected $title;
    protected $duration;
    protected $price;
    protected $station_id;

    public function __construct($company_id, $duration, $price, $title, $rate_card_id)
    {
        $this->rate_card_id = $rate_card_id;
        $this->station_id = $company_id;
        $this->duration = $duration;
        $this->price = $price;
        $this->title = $title;
    }

    private function updateRateCard()
    {
        $get_rate_card_by_id_service = new GetRateCardById($this->rate_card_id);
        $rate_card = $get_rate_card_by_id_service->getRateCardById();
        $rate_card->title = $this->title;
        $rate_card->save();
        return $rate_card;

    }

    private function deleteRateCardPrice()
    {
        return RateCardDuration::where('rate_card_id', $this->rate_card_id)->delete();
    }

    public function updateRateCardWithPrice()
    {
        \DB::transaction(function () use (&$rate_card) {
            $rate_card = $this->updateRateCard();
            $this->deleteRateCardPrice();
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
