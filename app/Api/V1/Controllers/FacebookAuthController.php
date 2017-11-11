<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\SocialAccountService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Auth;

class FacebookAuthController extends Controller
{
    /**
     * @return mixed
     */
    public function redirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * @param SocialAccountService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function callback(SocialAccountService $service)
    {
        $user = $service->createOrGetUser(Socialite::driver('facebook')->user());
        auth()->login($user);
        //$token = Auth::guard()->attempt($user);
        return response()->json(['current_user' => $user]);
    }
}
