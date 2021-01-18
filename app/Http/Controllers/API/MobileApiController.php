<?php

namespace App\Http\Controllers\API;

use App\Services\MobileService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MobileApiController extends Controller
{
    //
    public function sync(MobileService $service, Request $request)
    {
        $response = $service->sync($request);
        if ($response) {
            return response()->json(['data' =>  $response], 200);
        }
        return response()->json(['data' => ['message' => 'Wrong request format']], 500);
    }

    public function login(MobileService $service, Request $request)
    {
        $response = $service->login($request);
        if($response != ''){
            return response()->json(['data' =>  $response], 200);
        }
        return response()->json(['data' => ['message' => 'Wrong user/password']], 500);

    }
}
