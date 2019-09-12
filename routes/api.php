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

Route::middleware('auth:api')->any('/user', function (Request $request) {
    return $request->user();
});

Route::post('/admin/pipedrive/listen-api-deal', ['uses' => 'Admin\PipeDriveController@listenApiDeal', 'as' => 'api.pipedrive.listen.api.deal']);
Route::any('/admin/io/listen-api-docusign', ['uses' => 'Admin\IOController@listenApiDocusign', 'as' => 'api.io.listen.api.docusign']);
Route::post('/admin/creditcap/listen-api-qb', ['uses' => 'Admin\CreditCapController@listenApiQb', 'as' => 'api.credit.cap.api.qb']);