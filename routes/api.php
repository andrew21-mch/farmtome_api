<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FarmController;
use App\Http\Controllers\Api\IntrandsController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SupplyShopController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
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


// wrap all routes in sanctum middleware with v1 prefix
Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    /**  Products */
    Route::group(['prefix' => 'products'], function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });

    /**  Intrands */

    Route::group(['prefix' => 'intrands'], function () {
        Route::post('/', [IntrandsController::class, 'store']);
        Route::put('/{id}', [IntrandsController::class, 'update']);
        Route::delete('/{id}', [IntrandsController::class, 'destroy']);
    });

    /**  Orders */
    Route::group(['prefix' => 'orders'], function () {
        Route::post('/', [OrderController::class, 'store']);
        Route::put('/{id}', [OrderController::class, 'update']);
        Route::delete('/{id}', [OrderController::class, 'destroy']);
    });


    /**  Users */
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::get('/search/{key}', [UserController::class, 'search']);
        Route::post('logout', [AuthController::class, 'logout']);
    });

    /**  Transactions */
    Route::group(['prefix' => 'transactions'], function () {
        Route::post('/', [TransactionController::class, 'store']);
        Route::put('/{id}', [TransactionController::class, 'update']);
        Route::delete('/{id}', [TransactionController::class, 'destroy']);
    });

    /**  Farms **DONE** */
    Route::group(['prefix' => 'farms'], function () {
        Route::post('/', [FarmController::class, 'store']);
        Route::put('/{id}', [FarmController::class, 'update']);
        Route::delete('/{id}', [FarmController::class, 'destroy']);
    });

    /** Supplier Shops **DONE** */
    Route::group(['prefix' => 'supply_shops'], function () {
        Route::post('/', [SupplyShopController::class, 'store']);
        Route::put('/{id}', [SupplyShopController::class, 'update']);
        Route::delete('/{id}', [SupplyShopController::class, 'destroy']);
    });

});


// public routes
Route::group(['prefix' => 'v1'], function () {
    /**  Products */
    Route::group(['prefix' => 'products'], function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::get('/search/{key}', [ProductController::class, 'search']);
    });

    /**  Intrands */
    Route::group(['prefix' => 'intrands'], function () {
        Route::get('/', [IntrandController::class, 'index']);
        Route::get('/{id}', [IntrandController::class, 'show']);
        Route::get('/search/{key}', [IntrandController::class, 'search']);
    });

    /**  Users **DONE***/
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    /** Farms Public Routes **DONE** */

    Route::group(['prefix' => 'farms'], function (){
        Route::get('/', [FarmController::class, 'index']);
        Route::get('/{id}', [FarmController::class, 'show']);
        Route::get('/{userId}', [FarmControlloer::class, 'user_farms']);
        Route::get('/search/{key}', [FarmController::class, 'search']);
    });

    /** Supplier Shops  Public Routes **DONE***/
    Route::group(['prefix' => 'supply_shops'], function() {
        Route::get('/', [SupplyShopController::class, 'index']);
        Route::get('/{id}', [SupplyShopController::class, 'show']);
        Route::get('/{userId}', [SupplyShopController::class, 'user_supply_shops']);
        Route::get('/search/{key}', [SupplyShopController::class, 'search']);
    });
});
