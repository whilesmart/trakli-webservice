<?php

use App\Http\Controllers\API\v1\GroupController;
use App\Http\Controllers\API\v1\PartyController;
use App\Http\Controllers\API\v1\TransactionCategoryController;
use App\Http\Controllers\API\v1\TransactionController;
use App\Http\Controllers\API\v1\WalletController;
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

Route::prefix('v1')->group(function () {
    // Auth routes
    // Resource routes
    Route::apiResource('groups', GroupController::class);
    Route::apiResource('categories', TransactionCategoryController::class);
    Route::apiResource('parties', PartyController::class);
    Route::apiResource('wallets', WalletController::class);
    Route::apiResource('transactions', TransactionController::class);
});
