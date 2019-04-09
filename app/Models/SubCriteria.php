<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\Models\Criteria;

class SubCriteria extends Base
{
	protected $table = 'sub_criterias';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = ['criteria_id', 'name'];

    /**
     * Get sub criterias associated with record.
     */
    public function criteria()
    {
        return $this->belongsTo(Criteria::class);
    }
}
