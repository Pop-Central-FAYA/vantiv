<?php

namespace Vanguard\Services\RateCard;

use Vanguard\Models\RateCardDuration;

class UpdateRateCardService extends StoreBaseRateCard
{
    protected $rate_card_id;
    protected $request_data;

    public function __construct($rate_card_id, $request_data)
    {
        $this->rate_card_id = $rate_card_id;
        $this->request_data = $request_data;
    }

    private function updateRateCard()
    {
        $get_rate_card_by_id_service = new GetRateCardById($this->rate_card_id);
        $rate_card = $get_rate_card_by_id_service->getRateCardById();
        $rate_card->title = $this->request_data['name'];
        $rate_card->is_base = $this->request_data['is_base'];
        $rate_card->save();
        return $rate_card;

    }

    private function deleteRateCardPriceAndDuration()
    {
        return RateCardDuration::where('rate_card_id', $this->rate_card_id)->delete();
    }

    public function run()
    {
        \DB::transaction(function () use (&$rate_card) {
            self::toggleRatecard($this->request_data['company_id'], $this->request_data['is_base']);
            $rate_card = $this->updateRateCard();
            $this->deleteRateCardPriceAndDuration();
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
}
