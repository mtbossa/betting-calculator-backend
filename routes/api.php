<?php

use App\Http\Controllers\BetableMatchController;
use App\Http\Controllers\BetController;
use App\Models\Bet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\BetableMatch;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

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

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/users', function (Request $request) {
        return User::all();
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::group(['prefix' => 'matches', 'as' => 'betable_matches.'], function () {
        Route::get('/', [BetableMatchController::class, 'index'])->name('index');
        Route::post('/', [BetableMatchController::class, 'store'])->name('store');
        Route::get('/{match}', [BetableMatchController::class, 'show'])->name('show')->missing(function(Request $request) {
            return response(['message' => 'Match not found.'], Response::HTTP_NOT_FOUND);
        });
        Route::put('/{match}', [BetableMatchController::class, 'update'])->name('update');
        Route::delete('/{match}', [BetableMatchController::class, 'destroy'])->name('destroy');
    });

    Route::apiResource('matches.bets', BetController::class)
        ->shallow()
        ->missing(function(Request $request) {
            return response(['message' => 'Bet not found.'], Response::HTTP_NOT_FOUND);
        });
});
