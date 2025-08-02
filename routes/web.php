<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
Route::get('/', function () {
    return Inertia::render('Home');
});

//Auth Controller
Route::get('login', [AuthController::class, 'create'])->name('login');
Route::get('forgot-password', [AuthController::class, 'forgotPassword'])->name('login.forgot');
Route::post('forgot-password/send-otp', [AuthController::class, 'sendOtp'])->name('forgot.send');
Route::post('forgot-password/verify-otp', [AuthController::class, 'verifyOtp'])->name('forgot.verify');
Route::post('forgot-password/set-password', [AuthController::class, 'changePassword'])->name('forgot.password');
Route::post('login', [AuthController::class, 'store'])->name('login.store');
Route::delete('logout', [AuthController::class, 'destroy'])->name('login.destroy');



//Dashboard Controller
Route::group(['middleware' => 'auth', 'prefix' => 'dashboard'], function () {
    Route::get('',[DashboardController::class,'index'])->name('dashboard');
    Route::get('admin',[DashboardController::class,'admin'])->name('dashboard.admin');
    Route::get('manager',[DashboardController::class,'manager'])->name('dashboard.manager');
});
