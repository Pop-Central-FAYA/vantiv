<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $table = 'api_log';

    protected $fillable = ['request', 'response', 'ref', 'route'];

    public $timestamps = false;

    /**
     * Get the connection of the entity.
     *
     * @return string|null
     */
    public function getQueueableConnection()
    {
        // TODO: Implement getQueueableConnection() method.
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed $value
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value)
    {
        // TODO: Implement resolveRouteBinding() method.
    }

    public static function save_activity_log($request, $response, $route){
        $ref = strtotime(date('Y-m-d H:i:s')) . mt_rand(10000, 999999);
        $request = json_encode($request);
        $save = \DB::select("INSERT INTO api_log (request, response, route, ref) VALUES ('$request','$response','$route','$ref')");
        return $save;
    }
}