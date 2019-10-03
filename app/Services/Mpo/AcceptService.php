<?php

namespace Vanguard\Services\Mpo;

use Carbon\Carbon;
use DB;
use Vanguard\Libraries\Enum\MpoStatus;
use Vanguard\Models\MpoAccepter;
use Vanguard\Services\BaseServiceInterface;

class AcceptService implements BaseServiceInterface
{
    protected $data;
    protected $mpo;

    public function __construct($data, $mpo)
    {
        $this->data = $data;
        $this->mpo = $mpo;
    }

    public function run()
    {
        DB::transaction(function() {
            $mpo_accepter = new MpoAccepter();
            $mpo_accepter->first_name = $this->data['first_name'];
            $mpo_accepter->last_name = $this->data['last_name'];
            $mpo_accepter->email = $this->data['email'];
            $mpo_accepter->phone_number = $this->data['phone_number'];
            $mpo_accepter->mpo_id = $this->mpo->id;
            $mpo_accepter->save();

            $this->mpo->status = MpoStatus::ACCEPTED;
            $this->mpo->accepted_at = Carbon::now();
            $this->mpo->save();
        });
    }
}