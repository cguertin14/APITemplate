<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Image as ImageTable;
use Intervention\Image\ImageManagerStatic as Image;
use App\SocialAccountService;
use App\User;
use Carbon\Carbon;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Http\Request;
use Dingo\Api\Http\Response;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\GraphNodes\GraphUser;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Facebook\Facebook;
use Tymon\JWTAuth\JWTAuth;
use Config;
use Auth;

class FacebookAuthController extends BaseController
{
    /**
     * @param Request $request
     * @param JWTAuth $JWTAuth
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request,JWTAuth $JWTAuth)
    {
        $rules = ['access_token' => ['required']];
        $payload = $request->only('access_token');
        $validator = app('validator')->make($payload,$rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Could not log you in',$validator->errors());
        }

        $fb = new Facebook([
            'app_id' => '1524863834235048',
            'app_secret' => 'b13b7b1e240a120046ee69e2db46761e',
            'default_graph_version' => 'v2.2'
        ]);

        try {
            $response = $fb->get('/me?fields=id,first_name,last_name,email,birthday,picture.width(400).height(400),gender', $payload['access_token']);
        } catch (FacebookResponseException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (FacebookSDKException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $me = $response->getGraphUser();
        if ($user = User::where('facebook_id', $me['id'])->first()) {
            $token = $JWTAuth->fromUser($user);
            $user->statsUserToken()->create();
            return response()->json([
                'status' => 'ok',
                'token' => $token,
                'expires_in' => Auth::guard()->factory()->getTTL() * 60 . ' seconds'
            ],Response::HTTP_OK);
        } else {
            $defaultCover = Config::get('boilerplate.edit_cover_pic.default');
            Image::make($me->getProperty('picture')['url']);
            Image::make($defaultCover);

            $image = ImageTable::create([
                'id' => bin2hex(openssl_random_pseudo_bytes(7)),
                'image' => $me->getProperty('picture')['url'],
            ]);

            $coverImage = ImageTable::create([
                'id' => bin2hex(openssl_random_pseudo_bytes(7)),
                'image' => $defaultCover,
            ]);

            $user = User::create([
                'facebook_id' => $me['id'],
                'first_name' => $me['first_name'],
                'last_name' => $me['last_name'],
                'username' => strtolower(trim($me['first_name'] . $me['last_name'])),
                'email' => $me['email'],
                'genre' => $me['gender'],
                'birthdate' => $me->getBirthday()->format('Y-m-d'),
                'age' => Carbon::today()->diffInYears(Carbon::parse($me->getBirthday()->format('Y-m-d'))),
                'profile_image_id' => $image->id,
                'cover_image_id' => $coverImage->id
            ]);

            // Statistics for signUp
            $user->statsUser()->create();
            $user->statsUserToken()->create();

            $token = $JWTAuth->fromUser($user);
            return response()->json([
                'status' => 'ok',
                'user' => $user->fullUser(),
                'token' => $token,
                'expires_in' => Auth::guard()->factory()->getTTL() * 60 . ' seconds'
            ],Response::HTTP_CREATED);
        }
    }
}
