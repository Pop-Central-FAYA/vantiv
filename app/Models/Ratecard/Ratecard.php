<?php

namespace Vanguard\Models\Ratecard;

use Vanguard\Models\Base;
use Vanguard\Models\Company;
use Vanguard\Models\MediaProgram;
use Vanguard\Models\RateCardDuration;

class Ratecard extends Base
{
    protected $table = 'rate_cards';
    protected $fillable = ['title', 'company_id', 'duration', 'price', 'start_time', 'end_time', 'status'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function media_programs()
    {
        return $this->hasMany(MediaProgram::class);
    }

    public function rate_card_durations()
    {
        return $this->hasMany(RateCardDuration::class, 'rate_card_id');
    }

}
