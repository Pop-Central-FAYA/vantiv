<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class StatePopulation extends Base
{
    public $incrementing = true;
    protected $keyType = 'int';

	protected $table = 'state_populations';

    protected $fillable = ['year', 'state', 'count'];

}
