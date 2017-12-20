<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\BaseController;
use Dingo\Api\Http\Response;
use Namshi\JOSE\JWT;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Auth;
use JWTAuth;

class RefreshController extends BaseController
{
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token = Auth::guard()->refresh();
        $this->auth->user()->statsUserToken()->create();
        return response()->json([
            'status' => 'ok',
            'token' => $token,
            'expires_in' => Auth::guard()->factory()->getTTL() * 60 . ' seconds'
        ],Response::HTTP_OK);
    }

    public function validateToken()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'User not found'],404);
            }
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid'],Response::HTTP_BAD_REQUEST);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token is expired'],Response::HTTP_UNAUTHORIZED);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token not provided'],Response::HTTP_BAD_REQUEST);
        }
        $user->statsUserToken()->create();
        $user = $user->fullUser();
        return response()->json(compact('user'),Response::HTTP_OK);
    }
}