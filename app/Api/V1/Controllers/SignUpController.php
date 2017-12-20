<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\BaseController;
use Carbon\Exceptions\InvalidDateException;
use Config;
use App\User;
use Dingo\Api\Http\Response;
use Faker\Provider\Base;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\SignUpRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Image as ImageTable;
use Exception;

class SignUpController extends BaseController
{
    public function signUp(SignUpRequest $request, JWTAuth $JWTAuth)
    {
        try {
            $user = new User($request->all());
            if (base64_encode(base64_decode($request->profile_image, true)) === $request->profile_image) {
                $defaultCoverPic = Config::get('boilerplate.edit_cover_pic.default');
                Image::make($defaultCoverPic);
                Image::make($request->profile_image);

                $coverPic = ImageTable::create([
                    'id' => bin2hex(openssl_random_pseudo_bytes(7)),
                    'image' => $defaultCoverPic
                ]);
                $image = ImageTable::create([
                    'id' => bin2hex(openssl_random_pseudo_bytes(7)),
                    'image' => $request->profile_image
                ]);

                $user->profile_image_id = $image->id;
                $user->cover_image_id = $coverPic->id;

                if(!$user->save()) {
                    throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                // Statistics for signUp
                $user->statsUser()->create();
                $user->statsUserToken()->create();

                if(!Config::get('boilerplate.sign_up.release_token')) {
                    return response()->json([
                        'status' => 'ok',
                        'user' => $user->fullUser()
                    ], Response::HTTP_CREATED);
                }

                $token = $JWTAuth->fromUser($user);
                return response()->json([
                    'status' => 'ok',
                    'user' => $user->fullUser(),
                    'token' => $token
                ], Response::HTTP_CREATED);
            } else {
                return response()->json(['error' => 'The provided base64 image is invalid, please try again'],Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
            }
        } catch (QueryException $e) {
            return response()->json(['error' => 'Gender must be either male, female or other.'], Response::HTTP_NOT_ACCEPTABLE);
        } catch (Exception $e) {
            return response()->json(['error' => 'The processed image is too large.'],Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }
    }
}