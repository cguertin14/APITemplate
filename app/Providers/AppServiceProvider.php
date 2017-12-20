<?php

namespace App\Providers;

use App\Tools\ResponseHandling;
use Dingo\Api\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        app('Dingo\Api\Exception\Handler')->register(function (ModelNotFoundException $e){
            return response()->json(['error' => 'Resource not found!'], 404);
        });
        app('Dingo\Api\Exception\Handler')->register(function (QueryException $e){
            return response()->json(ResponseHandling::QUERY_ERROR,Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
