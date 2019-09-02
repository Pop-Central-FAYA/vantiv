<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Models\Campaign;
use Vanguard\Services\BaseServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;



class ListingService implements BaseServiceInterface
{
    protected $filters;
 
    public function __construct($filters)
    { 
      $this->filters = $filters;
    }

    public function run()
    {
        $campaigns =    static::apply(new Request($this->filters));
        return $campaigns;
    }

    private static function getArrayIndexFromRequest(Request $request, Builder $query)
    {
        foreach ($request->all() as $filterName => $value) {
                 $query->whereIn($filterName, $value);
        }
        return $query;
    }
    private static function getResults(Builder $query)
    {
        return $query->get();
    }
    public static function apply(Request $filters)
    {
        $query = static::getArrayIndexFromRequest( $filters, (new Campaign)->newQuery() );
        return static::getResults($query);
    }

}
