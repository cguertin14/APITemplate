<?php
/**
 * Created by PhpStorm.
 * User: charles
 * Date: 23/11/17
 * Time: 1:32 AM
 */

namespace App\Tools;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as BaseCollection;

class PaginateableCollection extends BaseCollection
{
    /**
     * @param $perPage
     * @param null $total
     * @param null $page
     * @param string $pageName
     * @return LengthAwarePaginator
     */
    public function paginate( $perPage, $total = null, $page = null, $pageName = 'page' )
    {
        $page = $page ?: LengthAwarePaginator::resolveCurrentPage( $pageName );

        return new LengthAwarePaginator( $this->forPage( $page, $perPage ), $total ?: $this->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }
}