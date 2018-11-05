<?php

namespace Vanguard\Http\Controllers\Api\Playout;

use Illuminate\Http\Request;

class BaseController extends \Vanguard\Http\Controllers\Api\BaseController {

    protected function verifyThatHeaderIsCorrect(Request $request) {
        $client_id = "c319ad43-47ba-4648-b79b-5f2af52b077e";
        return $client_id === $request->header('faya-client');;
    }

    protected function invalidAuthentication() {
        return response()->json(['error' => 'invalid authentication'], 401);
    }

    protected function resourceNonExistent($resource_name) {
        $msg = $resource_name . ' non existent';
        return response()->json(['error' => $msg], 404);
    }

}
