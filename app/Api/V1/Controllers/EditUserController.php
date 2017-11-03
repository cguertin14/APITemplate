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

class EditUserController extends BaseController
{
    /**
     * @param EditPasswordRequest $request
     */
    public function modifyPassword(EditPasswordRequest $request)
    {

    }

    /**
     * @param EditEmailRequest $request
     */
    public function modifyEmail(EditEmailRequest $request)
    {

    }

    /**
     * @param EditProfilePicRequest $request
     */
    public function modifyProfilePicture(EditProfilePicRequest $request)
    {

    }
}