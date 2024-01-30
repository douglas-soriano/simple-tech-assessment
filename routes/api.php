<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\FundController;

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

Route::middleware(['throttle:60,1'])->group(function () {

    Route::prefix('funds')->group(function () {
        Route::get('/potential-duplicates', [FundController::class, 'getPotentialDuplicates']);
        Route::get('/', [FundController::class, 'index']);
        Route::get('/{fund_id}', [FundController::class, 'show']);
        Route::put('/{fund_id}', [FundController::class, 'update']);
    });

});