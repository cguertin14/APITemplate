<?php

namespace App\Transformers;

use App\Message;
use App\User;
use League\Fractal;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;


class MessageTransformer extends TransformerAbstract
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
    public function transform(Message $message)
    {
        $message = $message->setVisible(['body','seen','created_at','sender'])->load('sender');
        $message['sender']['isowner'] = $message->conversation->event->organizer->id == $message->sender->id ? true : false;
        $message['sender']->setVisible(['username','isowner']);
        return $message->toArray();
    }
}
