<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EPSpaymentGatewayController;
use App\Http\Controllers\Api\BkashPaymentGatewayController;
use App\Http\Controllers\Backend\ProductController;

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

Route::get('/callback/bkash/',[BkashPaymentGatewayController::class,'bkashcallback_new']);

Route::get('/callback/eps/success',[EPSpaymentGatewayController::class,'EPSsuccess_callback']);
Route::get('/callback/eps/fail',[EPSpaymentGatewayController::class,'EPSfailed_callback']);
Route::get('/callback/eps/cancel',[EPSpaymentGatewayController::class,'EPScanceled_callback']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Remove the /api prefix - it's automatically added for api.php routes
Route::get('product-variant/{productId}/{colorId}/{sizeId}', [ProductController::class, 'getVariant']);
Route::get('product-variants/{productId}', [ProductController::class, 'getProductVariants']);