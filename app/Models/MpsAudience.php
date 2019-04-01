<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\Models\MpsAudienceProgramActivity;

class MpsAudience extends Base
{
	protected $table = 'mps_audiences';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = ['external_user_id', 'age', 'gender', 'region', 'lsm', 'state', 'social_class'];

    /**
     * Get program activities associated with the mps audience record.
     */
    public function programActivities()
    {
        return $this->hasMany(MpsAudienceProgramActivity::class);
    }
}