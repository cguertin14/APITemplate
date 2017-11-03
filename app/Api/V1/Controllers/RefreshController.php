<?php

namespace App\Api\V1\Controllers;

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

class RefreshController extends Controller
{
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token = Auth::guard()->refresh();

        return response()->json([
            'status' => 'ok',
            'token' => $token,
            'expires_in' => Auth::guard()->factory()->getTTL() * 60 . ' seconds'
        ]);
    }

    public function validateToken()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'User not found'],404);
            }
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid'],400);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token is expired'],401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token not provided'],400);
        }
        $user->setVisible(['first_name','last_name', 'id', 'name']);
        return response()->json(compact('user'));
    }
}