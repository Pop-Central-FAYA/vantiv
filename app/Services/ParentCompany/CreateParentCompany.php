<?php

namespace Vanguard\Services\ParentCompany;

use Vanguard\Models\ParentCompany;

class CreateParentCompany
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function createParentCompany()
    {
        $parent_company = new ParentCompany();
        $parent_company->name = $this->name;
        $parent_company->save();
        return $parent_company;
    }
}
