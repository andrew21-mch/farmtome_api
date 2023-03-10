<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to the API',
        'version' => '1.0.0',
        'description' => 'This is an api to link consumer, farmers, and suppliers together'.
                            'to make the process of buying and selling of agricultural products easier'.
                            'and more efficient.'.
                            'The api is built using laravel and is hosted on heroku and can be accessed at'.
                            'https://agroconnect-api.herokuapp.com/api/v1',
    ]);
});
