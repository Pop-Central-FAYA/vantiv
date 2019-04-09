<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\Models\SubCriteria;

class Criteria extends Base
{
	protected $table = 'criterias';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = ['name'];

    /**
     * Get sub criterias associated with record.
     */
    public function subCriterias()
    {
        return $this->hasMany(SubCriteria::class);
    }
}
