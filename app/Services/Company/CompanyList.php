<?php

namespace Vanguard\Services\Company;

use Vanguard\Libraries\Enum\CompanyTypeName;

class CompanyList
{
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function getCompanyListWithTypeAgency()
    {
        return \DB::table('companies')
                ->join('company_user', 'companies.id', '=', 'company_user.company_id')
                ->join('users', 'users.id', '=', 'company_user.user_id')
                ->join('company_types', 'company_types.id', '=', 'companies.company_type_id')
                ->select('users.firstname', 'users.lastname', 'users.id AS user_id', 'companies.id AS agency_id')
                ->selectRaw('CONCAT(users.firstname," ",users.lastname) AS name')
                ->where('company_types.name', CompanyTypeName::AGENCY)
                ->get();
    }
}
