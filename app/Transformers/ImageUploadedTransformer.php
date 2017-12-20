<?php

namespace App\Transformers;

use App\Image;
use League\Fractal;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;


class ImageUploadedTransformer extends TransformerAbstract
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
    public function transform(Image $image)
    {
        return [
            'status' => 'Image successfully uploaded!',
            'image_url' => env('IMG_API_URL') . $image->id
        ];
    }
}
