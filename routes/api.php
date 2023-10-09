<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DataPlanHistoryController;
use App\Http\Controllers\Api\OperatorCardController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\TopUpController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\TransferHistoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WebhookController;
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

// Route::middleware('jwt.verify')->get('/test', function (Request $request) {
//     return 'success';
// });

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('is-email-exist', [UserController::class, 'isEmailExist']);
Route::post('webhooks', [WebhookController::class, 'update']);

Route::group(['middleware' => 'jwt.verify'], function () {
    Route::post('top-ups', [TopUpController::class, 'store']);
    Route::post('transfers', [TransferController::class, 'store']);
    Route::post('data-plan-histories', [DataPlanHistoryController::class, 'store']);
    Route::get('operator-cards', [OperatorCardController::class, 'index']);
    Route::get('payment-methods', [PaymentMethodController::class, 'index']);
    Route::get('transfer-histories', [TransferHistoryController::class, 'index']);
    Route::get('transactions', [TransactionController::class, 'index']);

    Route::get('users', [UserController::class, 'show']);
    Route::get('users/{username}', [UserController::class, 'getUserByUsername']);
    Route::put('users', [UserController::class, 'update']);
});
