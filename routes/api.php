<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\EmployeeBioController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Employee Bio
Route::group(['prefix'=>'bio'], function () {
    Route::post('send-otp', [EmployeeBioController::class, 'sendOtp'])->name('api.bio.send');
    Route::post('verify-otp', [EmployeeBioController::class, 'verifyOtp'])->name('api.bio.verify');
    Route::get('show/{employee:mobile}', [EmployeeBioController::class, 'show'])->name('api.bio.show');
    Route::get('/download/{model}', [EmployeeBioController::class, 'downloadEngagementCard'])->name('api.bio.download-engagement-card');
});

