<?php

namespace App\Http\Controllers;

use App\Conversation;
use App\Event;
use App\Http\Requests\CreateConversationRequest;
use App\Http\Requests\EditConversationRequest;
use App\Message;
use App\Tools\ResponseHandling;
use App\Transformers\ConversationTransformer;
use App\Transformers\MessageTransformer;
use Dingo\Api\Http\Request;
use Dingo\Api\Http\Response;
use Illuminate\Database\QueryException;

class ConversationController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $conversations = $this->auth->user()->conversations()->paginate(10);
        return $conversations->count() == 0 ? response()->json(ResponseHandling::EMPTY_COLLECTION,ResponseHandling::HTTP_BAD_REQUEST) :
                                              $this->response->paginator($conversations,new ConversationTransformer);
    }

    /**
     * @param $keyword
     * @return Response|\Illuminate\Http\JsonResponse
     */
    public function search($keyword)
    {
        $this->searchVerification($keyword);
        // Look in three different relationships to find messages containing keyword
        // or sender name/username containing keyword. Also chek for conversation if
        // its name is like keyword. Then, paginate the results.
        $conversations = Conversation::query()
                                    ->whereHas('messages.sender', function ($query) use ($keyword) {
                                        $query->where('users.username','like',"%{$keyword}%")
                                              ->orWhere('users.first_name','like',"%{$keyword}%")
                                              ->orWhere('users.last_name','like',"%{$keyword}%");
                                    })
                                    ->orWhereHas('messages',function ($query) use ($keyword) {
                                        $query->where('messages.body','like',"%{$keyword}%");
                                    })
                                    ->orWhere('conversations.name','like',"%{$keyword}%")
                                    ->latest()
                                    ->paginate(10);

        return $conversations->count() == 0 ? response()->json(ResponseHandling::EMPTY_COLLECTION,ResponseHandling::HTTP_BAD_REQUEST) :
                                              $this->response->paginator($conversations,new ConversationTransformer);
    }

    /**
     * @param CreateConversationRequest $request
     * @return Response
     */
    public function store(CreateConversationRequest $request)
    {
        $data = $request->all();
        if (!$request->has('image_id')) {
            $data['image_id'] = Event::query()->findOrFail($request->input('event_id'))->organizer->profile_image_id;
        }
        $conversation = Conversation::query()->create($data);
        $this->auth->user()->conversations()->attach($conversation);
        return $this->response->item($conversation,new ConversationTransformer);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->response->paginator(Conversation::query()->findOrFail($id)->messages()->paginate(10),new MessageTransformer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!$request->has('event_id')) {
            if ($request->has('muted') || $request->has('name')) {
                $conversation = Conversation::query()->findOrFail($id);
                $conversation->update($request->all());
                return $this->response->item($conversation,new ConversationTransformer)->setStatusCode(ResponseHandling::HTTP_CREATED);
            } else {
                return response()->json([
                    'error' => 'You need to provide either a value for muted or name.'
                ],ResponseHandling::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            return response()->json([
                'error' => 'You cannot modify the event attached to this conversation.'
            ], ResponseHandling::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Conversation::query()->findOrFail($id)->delete();
        return response()->json(ResponseHandling::RESOURCE_DELETED,ResponseHandling::HTTP_NO_CONTENT);
    }
}
