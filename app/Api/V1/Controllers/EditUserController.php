<?php
/**
 * Created by PhpStorm.
 * User: guertz
 * Date: 11/2/17
 * Time: 8:18 PM
 */

namespace App\Api\V1\Controllers;


use App\Api\V1\Requests\EditEmailRequest;
use App\Api\V1\Requests\EditPasswordRequest;
use App\Api\V1\Requests\EditProfilePicRequest;
use App\Http\Controllers\BaseController;
use Auth;
use Exception;

class EditUserController extends BaseController
{
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
            return response()->json(['status' => 'Password successfully updated!']);
        } else {
            return response()->json(['error' => 'Wrong password!'],400);
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
            return response()->json(['status' => 'Email successfully updated!']);
        } else {
            return response()->json(['error' => 'Wrong password!'],400);
        }
    }

    /**
     * @param EditProfilePicRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function modifyProfilePicture(EditProfilePicRequest $request)
    {
        try {
            if (base64_encode(base64_decode($request->newpicture, true)) === $request->newpicture) {
                $user = Auth::guard()->user();
                $user->photo = $request->newpicture;
                $user->save();
                return response()->json(['status' => 'Image successfully uploaded!']);
            } else {
                return response()->json(['error' => 'The provided base64 image is invalid, please try again'],400);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occured, please try again'],400);
        }
    }
}