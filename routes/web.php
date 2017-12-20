<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Intervention\Image\ImageManagerStatic as Image;
use App\Image as ImageTable;

Route::get('reset_password/{token}', ['as' => 'password.reset', function($token) {

    // implement your reset password route here!

}]);

Route::get('/image/{id}', function ($id) {
    if(!$upload = ImageTable::query()->findOrFail($id)){
        return response()->json(['error' => 'Image not found!'], 404);
    }
    $img = Image::cache(function($image) use ($upload) {
        $image->make($upload->image);
    });
    return Image::make($img)->response('jpg');
});
