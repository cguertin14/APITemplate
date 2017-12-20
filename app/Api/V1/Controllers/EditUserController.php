<?php
/**
 * Created by PhpStorm.
 * User: guertz
 * Date: 11/2/17
 * Time: 8:18 PM
 */

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\EditCoverPicRequest;
use App\Api\V1\Requests\EditEmailRequest;
use App\Api\V1\Requests\EditPasswordRequest;
use App\Api\V1\Requests\EditProfilePicRequest;
use App\Http\Controllers\BaseController;
use App\Transformers\ImageUploadedTransformer;
use App\Transformers\UserTransformer;
use Auth;
use Dingo\Api\Http\Request;
use Dingo\Api\Http\Response;
use Exception;
use App\Image as ImageTable;
use function GuzzleHttp\Psr7\str;
use Illuminate\Database\QueryException;
use Intervention\Image\ImageManagerStatic as Image;

class EditUserController extends BaseController
{
    /**
     * @param Request $request
     * @return Response|\Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        if ($request->has('phone') || $request->has('country') || $request->has('city') ||
            $request->has('gender') || $request->has('username') || $request->has('device_token')) {
            try {
                $user = $this->auth->user();
                $user->update($request->all());
                return $this->response->item($user->fullUser(), new UserTransformer);
            } catch (QueryException $e) {
                return response()->json(['error' => 'Gender must be either male, female or other.'], Response::HTTP_NOT_ACCEPTABLE);
            }
        } else {
            return response()->json([
                'error' => 'You need to provide either a new phone number, country, city or gender.'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param EditPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function modifyPassword(EditPasswordRequest $request)
    {
        if (Auth::guard()->attempt(['email' => Auth::guard()->user()->email,'password' => $request->old_password])) {
            $user = Auth::guard()->user();
            $user->password = $request->password; ///// HASH PASSWORD IS DONE IN THE MODEL 'USER' ITSELF.
            $user->save();
            return response()->json(['status' => 'Password successfully updated!'],Response::HTTP_CREATED);
        } else {
            return response()->json(['error' => 'Wrong password!'],Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param EditEmailRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function modifyEmail(EditEmailRequest $request)
    {
        if (Auth::guard()->attempt(['email' => Auth::guard()->user()->email,'password' => $request->password])) {
            $user = Auth::guard()->user();
            $user->email = $request->email;
            $user->save();
            return response()->json(['status' => 'Email successfully updated!'],Response::HTTP_CREATED);
        } else {
            return response()->json(['error' => 'Wrong password!'],Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param EditProfilePicRequest $request
     * @return \Dingo\Api\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function modifyProfilePicture(EditProfilePicRequest $request)
    {
        try {
            if (base64_encode(base64_decode($request->newpicture, true)) === $request->newpicture) {
                $user = Auth::guard()->user();
                Image::make($request->newpicture);
                if ($user->profile_image_id) {
                    $image = ImageTable::findOrFail($user->profile_image_id);
                    $image->image = $request->newpicture;
                    $image->save();
                } else {
                    $image = ImageTable::create([
                        'id' => bin2hex(openssl_random_pseudo_bytes(7)),
                        'image' => $request->newpicture
                    ]);
                    $user->profile_image_id = $image->id;
                    $user->save();
                }
                return $this->response->item($image,new ImageUploadedTransformer)->setStatusCode(Response::HTTP_CREATED);
            } else {
                return response()->json(['error' => 'The provided base64 image is invalid, please try again'],Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'The processed image is too large.'],Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }
    }

    /**
     * @param EditCoverPicRequest $request
     * @return \Dingo\Api\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function modifyCoverPicture(EditCoverPicRequest $request)
    {
        try {
            if (base64_encode(base64_decode($request->newpicture, true)) === $request->newpicture) {
                $user = Auth::guard()->user();
                Image::make($request->newpicture);
                if ($user->cover_image_id) {
                    $image = ImageTable::findOrFail($user->cover_image_id);
                    $image->image = $request->newpicture;
                    $image->save();
                } else {
                    $image = ImageTable::create([
                        'id' => bin2hex(openssl_random_pseudo_bytes(7)),
                        'image' => $request->newpicture
                    ]);
                    $user->cover_image_id = $image->id;
                    $user->save();
                }
                return $this->response->item($image,new ImageUploadedTransformer)->setStatusCode(Response::HTTP_CREATED);
            } else {
                return response()->json(['error' => 'The provided base64 image is invalid, please try again'],Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'The processed image is too large.'],Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }
    }
}