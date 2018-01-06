<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PeeController extends Controller
{

    public function calculate(PeeRequest $request)
    {
        return response()->json([ "status"=> "success", "value"=> 3]);
    }
}
