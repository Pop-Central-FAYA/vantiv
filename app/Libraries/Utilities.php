<?php

namespace Vanguard\Libraries;

use Illuminate\Support\Facades\DB;

class Utilities {

    public static function switch_db($db)
    {
        switch ($db){
            case 'local':
                return DB::connection('mysql');
                break;
            case 'api':
                return DB::connection('mysql-2');
                break;
            case 'reports':
                return DB::connection('api_db');
                break;
            default;
                return null;
                break;
        }
    }

    public static function clean_num( $num ){
        $number  = $num;
        $trim = rtrim($number, '.');
        return $trim;
    }

}
