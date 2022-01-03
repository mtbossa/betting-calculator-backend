<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\BetableMatch;
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
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/matches', function (Request $request) {
        $match = Auth::user()->matches()->create([
            'team_one' => $request->team_one,
            'team_two' => $request->team_two,
        ]);
    });

    Route::delete('/matches/{match}', function (BetableMatch $match) {
        $match->delete();
    });
});
