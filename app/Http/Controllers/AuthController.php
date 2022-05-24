<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * @param RegisterRequest $request 
     * @return string 
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->only(
            'first_name',
            'last_name',
            'email'
        ) + [
            'password' => Hash::make($request->input('password')),
            'is_admin' => 1
        ]);

        return response($user, Response::HTTP_CREATED);

        // return "hello";
    }
}
