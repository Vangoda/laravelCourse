<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateInfoRequest;
use App\Models\User;
use Auth;
use Cookie;
use Exception;
use Hash;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\InvalidCastException;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use RuntimeException;
use LogicException;
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

    /**
     * @param Request $request 
     * @return HttpResponse|ResponseFactory 
     * @throws RuntimeException 
     * @throws BindingResolutionException 
     * @throws Exception 
     * @throws InvalidCastException 
     * @throws LogicException 
     */
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response(
                [
                    'error' => 'Invalid credentials!'
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Get authenticated user
        $user = Auth::user();

        // Generate JWT token
        $jwt = $user->createToken('token', ['admin'])->plainTextToken;

        $cookie = cookie('jwt', $jwt, 1440);

        // Return authenticated user token in a cookie
        return response([
            'message' => 'Success, returned token in a cookie.'
        ])->withCookie($cookie);
    }

    /**
     * @param Request $request 
     * @return HttpResponse|ResponseFactory 
     * @throws BindingResolutionException 
     */
    public function logout(Request $request)
    {
        // Removes the cookie and revokes the token
        $cookie = Cookie::forget('jwt');
        $request->user()->currentAccessToken()->delete();

        return response([
            'message' => 'Successfully logged out. Cookie removed.'
        ])->withCookie($cookie);
    }

    /**
     * @param Request $request 
     * @return mixed 
     */
    public function user(Request $request)
    {
        // Returns user
        return $request->user();
    }

    public function updateInfo(UpdateInfoRequest $request)
    {
        // Update users email, first_name and last_name

        // Get user from request
        $user = $request->user();
        /*  Update user information
        *   
        *   $request->only() returns assoc array from requested matching given
        *   keys. Empty or non existing keys simply won't be added to output
        *   array.
        *   $user->update() updates model properties in db which match given
        *   input array keys to columns in db table.
        */
        $user->update($request->only('first_name', 'last_name', 'email'));

        return response($user, Response::HTTP_ACCEPTED);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        // Change users password

        $user = $request->user();
        // Hash and update the password
        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);
    }
}
