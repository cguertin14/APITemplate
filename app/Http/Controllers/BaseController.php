<?php
/**
 * Created by PhpStorm.
 * User: guertz
 * Date: 10/30/17
 * Time: 3:35 PM
 */

namespace App\Http\Controllers;

use Dingo\Api\Routing\Helpers;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Item;

class BaseController extends Controller
{
    use Helpers;
    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * BaseController constructor.
     * @param Manager $fractal
     */
    public function __construct(Manager $fractal) {
        $this->fractal = $fractal;
    }

    /**
     * @param $resource
     * @param $transformer
     * @return array
     */
    public function transformItem($resource, $transformer)
    {
        return $this->fractal->createData(new Item($resource, $transformer))->toArray();
    }

    /**
     * @param $resource
     * @param $transformer
     * @return array
     */
    public function transformCollection($resource, $transformer)
    {
        return $this->fractal->createData(new Collection($resource, $transformer))->toArray();
    }

    /**
     * @param $paginator
     * @param $transformer
     * @return array
     */
    public function paginate($paginator, $transformer)
    {
        $data = $paginator->getCollection();
        $resources = new Collection($data, $transformer);
        $resources->setPaginator(new IlluminatePaginatorAdapter($paginator));
        return $this->fractal->createData($resources)->toArray();
    }
}