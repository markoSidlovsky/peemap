<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PeeController extends Controller
{

    public function calculate(PeeRequest $request)
    {
        $responseValue = round($request->age * 0.1 + $request->weight * 0.02 + $request->height * 0.01- $request->consumption * 0.1, 1);
        return response()->json([ "status"=> "success", "value"=> $responseValue>0?$responseValue:0]);
    }
}
