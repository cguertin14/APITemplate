<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateNotificationRequest;
use App\Notification;
use App\Tools\ResponseHandling;
use App\Transformers\NotificationTransformer;
use App\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Http\Response;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    /**
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        return $this->response->collection($this->auth->user()->notifications()->get(), new NotificationTransformer);
    }


    public function create(CreateNotificationRequest $request)
    {
        if (!$user = User::query()->where('device_token',$request->input('device_token'))->first()) {
            return response()->json(['error' => 'Wrong device token provided, no user associated to it.'], ResponseHandling::HTTP_NOT_ACCEPTABLE);
        }
        Notification::query()->create($request->all());
        return response()->json(['status' => 'Notification successfully created!'], ResponseHandling::HTTP_CREATED);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $this->auth->user()->notifications()->findOrFail($id)->delete();
        return response()->json(['status' => 'Notification successfully deleted!'], ResponseHandling::HTTP_NO_CONTENT);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear()
    {
        $notifs = $this->auth->user()->notifications();
        if ($notifs->get()->count() > 0) {
            $notifs->delete();
            return response()->json([
                'status' => 'Notifications successfully cleared!'
            ], ResponseHandling::HTTP_NO_CONTENT);
        } else {
            return response()->json([
                'error' => 'You appear to have no notifications to clear.'
            ], ResponseHandling::HTTP_FORBIDDEN);
        }
    }
}