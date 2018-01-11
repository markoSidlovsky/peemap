<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PeeController extends Controller
{

    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "age" => "required|numeric|min:1|max:150",
            "weight" => "required|numeric|min:10|max:280",
            "height" => "required|numeric|min:10|max:500",
            "consumption" => "required|numeric|min:0|max:100"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $responseValue = round($request->age * 0.1 + $request->weight * 0.02 + $request->height * 0.01- $request->consumption * 0.1, 1);
        return response()->json([ "status"=> "success", "value"=> $responseValue>0?$responseValue:0]);
    }
}
