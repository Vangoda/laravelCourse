<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @param RegisterRequest $request 
     * @return string 
     */
    public function register(RegisterRequest $request)
    {
        return "hello";
    }
}
