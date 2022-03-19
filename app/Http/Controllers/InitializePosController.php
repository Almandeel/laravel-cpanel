<?php

namespace App\Http\Controllers;

use App\Jobs\CreatePos;
use Illuminate\Http\Request;

class InitializePosController extends Controller
{
    public function initialize(Request $request)
    {
        dispatch(new CreatePos($request->domain))->afterResponse();
        return "waitting......";
    }
}
