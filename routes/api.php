<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;
use App\Models\FunWalk;


use App\Http\Controllers\Admin\CandidateRegistrationController;

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

Route::post('/callback-ecocash', [RegistrationController::class, 'ecoCashCallBackUrl']);

// Fun Walk payment callbacks
use App\Http\Controllers\Admin\FunWalkPaymentController;
Route::post('/fun-walk/ecocash-callback', [FunWalkPaymentController::class, 'ecoCashCallback']);
Route::post('/fun-walk/mpesa-callback', [FunWalkPaymentController::class, 'mpesaCallback']);

// Fun Walk API endpoint
Route::get('/fun-walks', function () {
    return FunWalk::where('status', 'active')->get(['id', 'title', 'date', 'location', 'price']);
});






// Admin Routes
Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',

], function () {
    Route::post('candidates-registered', [CandidateRegistrationController::class, 'registerdCandidates'])->name('candidate-registation.registerdCandidates');
});






Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
