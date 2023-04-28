<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('{email}/forgot_password', [AuthController::class, 'forgotPassword']);
Route::get('{email}/verify/{verification_code}', [AuthController::class, 'verify']);
Route::get('{email}/resend_verification', [AuthController::class, 'resendVerification']);
Route::patch('{email}/reset_password', [AuthController::class, 'resetPassword']);
