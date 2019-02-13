<?php

namespace Vanguard\Services\Walkin;

use Vanguard\Libraries\Utilities;

class WalkInLists
{
    protected $company_id;
    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function getWalkInList()
    {
        return Utilities::switch_db('api')->table('walkIns')
                            ->join('users', 'users.id', '=', 'walkIns.user_id')
                            ->join('companies', 'companies.id', '=', 'walkIns.company_id')
                            ->select('walkIns.user_id', 'walkIns.id', 'users.firstname', 'users.lastname',
                            'users.phone_number', 'walkIns.location', 'walkIns.company_logo', 'walkIns.company_name AS company_name',
                                'walkIns.time_created', 'users.email', 'walkIns.image_url', 'companies.name AS company'
                            )
                            ->when(is_array($this->company_id), function($query) {
                                return $query->whereIn('walkIns.company_id', $this->company_id);
                            })
                            ->when(!is_array($this->company_id), function($query) {
                                return $query->where('walkIns.company_id', $this->company_id);
                            })
                            ->get();
    }
}
