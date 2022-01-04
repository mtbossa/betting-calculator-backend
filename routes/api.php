<?php

use App\Http\Controllers\BetableMatchController;
use App\Models\Bet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\BetableMatch;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

    Route::group(['prefix' => 'matches', 'as' => 'matches.'], function () {
        Route::get('/', [BetableMatchController::class, 'index'])->name('index');
        Route::post('/', [BetableMatchController::class, 'store'])->name('store');
        Route::delete('/{match}', [BetableMatchController::class, 'destroy'])->name('destroy');
    });

    Route::post('/matches/{match}/bets', function (BetableMatch $match, Request $request) {
        $bet = $match->bets()->create([
            'winner_team' => $request->winner_team,
            'odd' => $request->odd,
            'amount' => $request->amount,
            'currency' => $request->currency,
        ]);
    });

    Route::delete('/matches/{match}/bets/{bet}', function (BetableMatch $match, Bet $bet) {
        $bet->delete();
    });
});
