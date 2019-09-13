<?php

namespace Vanguard\Models;

use Vanguard\Models\MpsAudienceProgramActivity;

use EloquentFilter\Filterable;

class MpsAudience extends Base
{
    use Filterable;

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