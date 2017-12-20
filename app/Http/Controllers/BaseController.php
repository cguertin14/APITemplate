<?php
/**
 * Created by PhpStorm.
 * User: guertz
 * Date: 10/30/17
 * Time: 3:35 PM
 */

namespace App\Http\Controllers;

use App\Tools\ResponseHandling;
use Dingo\Api\Routing\Helpers;
use \App\Tools\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Item;
use Dingo\Api\Http\Response;

class BaseController extends Controller
{
    use Helpers;
    use Helper;

    /**
     * @param string $keyword
     * @return Response|JsonResponse
     */
    public function search($keyword) {}

    /**
     * @param string $keyword
     * @return JsonResponse
     */
    public function searchVerification($keyword)
    {
        if (!isset($keyword) || strlen($keyword) < 3) {
            return response()->json(ResponseHandling::KEYWORD_LENGTH,ResponseHandling::HTTP_BAD_REQUEST);
        }
    }
}