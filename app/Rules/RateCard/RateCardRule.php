<?php

namespace Vanguard\Rules\RateCard;

use Illuminate\Contracts\Validation\Rule;
use Vanguard\Models\Ratecard\Ratecard;

class RateCardRule implements Rule
{
    protected $name;
    protected $company_id;

    public function __construct($name, $company_id)
    {
        $this->name = $name;
        $this->company_id = $company_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $check_rate = Ratecard::where([
            ['slug', str_slug($this->name)],
            ['company_id', $this->company_id]
        ])->first();
        if($check_rate){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Rate card already exists';
    }
}
