<?php

use App\Http\Controllers\IntrandController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
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

// structure of the api


Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome Agroconnect API',
        'version' => '1.0.0',
        'description' => 'This is an api to link consumer, farmers, and suppliers together'.
                            'to make the process of buying and selling of agricultural products easier'.
                            'and more efficient.'.
                            'The api is built using laravel and is hosted on heroku and can be accessed at '.
                            'https://agroconnect-api.herokuapp.com/api/v1',
    ]);
});


// auth middleware

Route::group(['prefix' => 'v1', ['middleware' => 'auth:sanctum']], function () {

    /**  Products */
    Route::group(['prefix' => 'products'], function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
        Route::get('/search/{key}', [ProductController::class, 'search']);
    });

    /**  Intrands */

    Route::group(['prefix' => 'intrands'], function () {
        Route::get('/', [IntrandsController::class, 'index']);
        Route::get('/{id}', [IntrandsController::class, 'show']);
        Route::post('/', [IntrandsController::class, 'store']);
        Route::put('/{id}', [IntrandsController::class, 'update']);
        Route::delete('/{id}', [IntrandsController::class, 'destroy']);
        Route::get('/search/{key}', [IntrandController::class, 'search']);
    });

    /**  Orders */
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::post('/', [OrderController::class, 'store']);
        Route::put('/{id}', [OrderController::class, 'update']);
        Route::delete('/{id}', [OrderController::class, 'destroy']);
        Route::get('/search/{key}', [OrderController::class, 'search']);
    });


    /**  Users */
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::get('/search/{key}', [UserController::class, 'search']);
    });

    /**  Transactions */
    Route::group(['prefix' => 'transactions'], function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::get('/{id}', [TransactionController::class, 'show']);
        Route::post('/', [TransactionController::class, 'store']);
        Route::put('/{id}', [TransactionController::class, 'update']);
        Route::delete('/{id}', [TransactionController::class, 'destroy']);
        Route::get('/search/{key}', [TransactionController::class, 'search']);
    });
});
