<?php

namespace App\Transformers;

use App\Conversation;
use League\Fractal;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;


class ConversationTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * Transform object into a generic array
     *
     * @var $resource
     * @return array
     */
    public function transform(Conversation $conversation)
    {
        $conversation = $conversation->fullConversation()->setVisible(['name','image_url','latest_message']);
        if ($latestMessage = $conversation->messages()->latest()->first()) {
            $conversation['latest_message'] = $latestMessage->setVisible(['body', 'seen', 'created_at', 'sender'])->load('sender');
            $conversation['latest_message']['sender']->setVisible(['username']);
        }
        return $conversation->toArray();
    }
}
