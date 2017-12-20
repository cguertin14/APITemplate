<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\BaseController;
use App\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Http\Request;
use Dingo\Api\Http\Response;
use Faker\Provider\Base;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Auth;

class UserController extends BaseController
{
    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['verifyEmail','verifyPhone','verifyUsername']]);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $currentuser = $this->auth->user()->fullUser()->setHidden([
            'id','profile_image_id','cover_image_id','created_at','updated_at','facebook_id','password'
        ]);
        return response()->json(['current_user' => $currentuser],Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request)
    {
        $rules = ['email' => ['required','email']];
        $payload = $request->only('email');
        $validator = app('validator')->make($payload,$rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Could not verify email',$validator->errors());
        }

        if (User::query()->where('email',$request->email)->exists()) {
            return response()->json(['error' => 'Email already exists'],Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['status' => 'Email is available'],Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyPhone(Request $request)
    {
        $rules = ['phone' => ['required']];
        $payload = $request->only('phone');
        $validator = app('validator')->make($payload,$rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Could not verify phone',$validator->errors());
        }

        if (User::query()->where('phone',$request->phone)->exists()) {
            return response()->json(['error' => 'Phone already exists'],Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['status' => 'Phone is available'],Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyUsername(Request $request)
    {
        $rules = ['username' => ['required']];
        $payload = $request->only('username');
        $validator = app('validator')->make($payload,$rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Could not verify username',$validator->errors());
        }

        if (User::query()->where('username',$request->username)->exists()) {
            return response()->json(['error' => 'Username already exists'],Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['status' => 'Username is available'],Response::HTTP_OK);
    }
}
