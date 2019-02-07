<?php

namespace Vanguard\Services\Company;

class UserCompanyByChannel
{
    protected $channel_id;
    protected $user_id;

    public function __construct($channel_id, $user_id)
    {
        $this->channel_id = $channel_id;
        $this->user_id = $user_id;
    }

    public function baseQuery()
    {
        return \DB::table('companies')
                    ->join('channel_company', 'channel_company.company_id', '=', 'companies.id')
                    ->join('company_user', 'company_user.company_id', '=', 'companies.id')
                    ->where([
                        ['channel_company.channel_id', $this->channel_id],
                        ['company_user.user_id', $this->user_id]
                    ]);
    }

    public function getListOfCompany()
    {
        return $this->baseQuery()->get();
    }

    public function getListOfCompanyIds()
    {
        return $this->baseQuery()->select('companies.id')
                                ->get();
    }
}
