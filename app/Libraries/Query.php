<?php

namespace Vanguard\Libraries;
use Illuminate\Support\Facades\DB;

/**
 * This class can return the raw sql of a query (With the bindings injected)
 * This is HEAVILY influenced by code in Telescope by Laravel
 */
class Query
{
    public static function getSql($query)
    {
        $sql = static::replaceBindings($query);
        return $sql;
    }

    private static function replaceBindings($query)
    {
        $sql = $query->toSql();
        $formatted_bindings = static::formatBindings($query);

        foreach ($formatted_bindings as $key => $binding) {
            $regex = is_numeric($key)
                ? "/\?(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/"
                : "/:{$key}(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/";

            if ($binding === null) {
                $binding = 'null';
            } elseif (! is_int($binding) && ! is_float($binding)) {
                $binding = DB::connection()->getPdo()->quote($binding);
            }

            $sql = preg_replace($regex, $binding, $sql, 1);
        }

        return $sql;
    }

    private static function formatBindings($query)
    {
        return DB::connection()->prepareBindings($query->getBindings());
    }
}
