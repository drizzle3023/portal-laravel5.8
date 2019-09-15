<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('login', 'ApiController@doLogin');
});

Route::middleware('api-auth')->group(function (){
    Route::post('get-categories', 'APIController@getCategoriesByClient');
    Route::post('get-products', 'APIController@getProductsByCategory');
    Route::post('get-product-detail', 'APIController@getProductDetail');
    Route::post('get-client-detail', 'APIController@getClientDetail');
});
