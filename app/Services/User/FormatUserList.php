<?php

namespace Vanguard\Services\User;
use Vanguard\Models\Company;

class FormatUserList
{
    public function getCompanyName($company_id_list)
    {
        if ($company_id_list) {
            $companies = Company::select('name')->whereIn('id', $company_id_list)->get();
            return $companies->implode("name", ", ");
        }
        return "";
    }

    public function roleLabel($roles)
    {
        $role_label = [];
        foreach ($roles as $role){
            $role_label[] = $role ? ucwords(str_replace('_',' ', explode('.', $role)[1])) : '';
        }
        return $role_label;
    }

    public function formatLable($roles)
    {
        $rolex = [];
        foreach ($roles as $role){
        $new = (object)[
               'name' => $role,
               'label' => ucwords(str_replace('_',' ', explode('.', $role)[1])),
        ];
        array_push($rolex, $new);
        }
        return $rolex;
    }
   
}
