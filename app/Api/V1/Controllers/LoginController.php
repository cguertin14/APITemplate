<?php

namespace App\Api\V1\Controllers;


use App\Http\Controllers\BaseController;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Http\Response;
use function GuzzleHttp\Psr7\copy_to_string;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Auth;
use Hash;

class LoginController extends BaseController
{
    /**
     * Log the user in
     *
     * @param LoginRequest $request
     * @param $JWTAuth $JWTAuth
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request, JWTAuth $JWTAuth)
    {
        $field = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $request->merge([$field => $request->input('login')]);
        $credentials = $request->only($field, 'password');

        if ($field === 'email') {
            $validator = app('validator')->make($request->only('login'),['login' => ['email']]);
            if ($validator->fails()) {
                throw new StoreResourceFailedException('Could not log you in',$validator->errors());
            }
        }
        try {
            $token = Auth::guard()->attempt($credentials);

            if(!$token) {
                //throw new AccessDeniedHttpException();
                return response()->json(['error' => 'Wrong credentials.'],400);
            }

        } catch (JWTException $e) {
            throw new HttpException(500);
        }

        // Statistics for login
        Auth::guard()->user()->statsUserToken()->create();

        return response()
            ->json([
                'status' => 'ok',
                'token' => $token,
                'expires_in' => Auth::guard()->factory()->getTTL() * 60 . ' seconds'
            ],Response::HTTP_OK);
    }
}
