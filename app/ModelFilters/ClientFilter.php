<?php

namespace Vanguard\ModelFilters;

use EloquentFilter\ModelFilter;

class ClientFilter extends ModelFilter
{

    public function company($company_id)
    {
        return $this->where('company_id', $company_id);
    }

    public function createdBy($created_by)
    {
        return $this->where('created_by', $created_by);
    }
}
