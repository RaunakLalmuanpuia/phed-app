<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

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

//Profile Controller
Route::group(['middleware'=>'auth','prefix' => 'profile'], function () {
    Route::get('edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('edit-password', [ProfileController::class, 'editPassword'])->name('profile.edit-password');
    Route::put('update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
});



Route::group(['prefix' => 'admin','middleware'=>'auth'], function () {

    Route::group(['prefix' => 'user'], function () {
        Route::get('', [UserController::class, 'index'])->name('user.index');
        Route::get('json-index', [UserController::class, 'jsonAll'])->name('user.json-index');
        Route::get('create', [UserController::class, 'create'])->name('user.create');
        Route::post('store', [UserController::class, 'store'])->name('user.store');
        Route::get('edit/{model}', [UserController::class, 'edit'])->name('user.edit');
        Route::put('update/{model}', [UserController::class, 'update'])->name('user.update');
        Route::get('{model}/show', [UserController::class, 'show'])->name('user.show');
        Route::delete('{model}', [UserController::class, 'destroy'])->name('user.destroy');
    });

    Route::group(['prefix' => 'role'], function () {
        Route::get('', [RoleController::class, 'index'])->name('role.index');
        Route::get('{model}', [RoleController::class, 'show'])->name('role.show');
        Route::put('{model}', [RoleController::class, 'update'])->name('role.update');
    });

});
