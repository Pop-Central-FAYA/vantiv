<?php

namespace Vanguard\Services\Walkin;

use Vanguard\Libraries\Utilities;

class WalkInLists
{
    protected $broadcaster_id;
    protected $agency_id;

    public function __construct($broadcaster_id, $agency_id)
    {
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
    }

    public function getWalkInList()
    {
        return Utilities::switch_db('api')->table('walkIns')
                            ->join('users', 'users.id', '=', 'walkIns.user_id')
                            ->select('walkIns.user_id', 'walkIns.id', 'users.firstname', 'users.lastname',
                            'users.phone_number', 'walkIns.location', 'walkIns.company_logo', 'walkIns.company_name',
                                'walkIns.time_created', 'users.email', 'walkIns.image_url'
                            )
                            ->when($this->broadcaster_id, function($query) {
                                return $query->where('walkIns.broadcaster_id', $this->broadcaster_id);
                            })
                            ->when($this->agency_id, function($query) {
                                return $query->where('walkIns.agency_id', $this->agency_id);
                            })
                            ->get();
    }
}
