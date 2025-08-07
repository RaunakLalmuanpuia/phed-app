<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MISController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\DeletionController;



Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

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
    Route::group(['prefix' => 'office'], function () {
        Route::get('', [OfficeController::class, 'index'])->middleware('can:view-office')->name('office.index');
        Route::get('json-index', [OfficeController::class, 'jsonAll'])->middleware('can:view-anyoffice')->name('office.json-index');
        Route::get('create', [OfficeController::class, 'create'])->middleware('can:create-office')->name('office.create');
        Route::post('store', [OfficeController::class, 'store'])->middleware('can:create-office')->name('office.store');
        Route::get('edit/{model}', [OfficeController::class, 'edit'])->middleware('can:edit-office')->name('office.edit');
        Route::put('update/{model}', [OfficeController::class, 'update'])->middleware('can:edit-office')->name('office.update');
        Route::get('{model}/show', [OfficeController::class, 'show'])->middleware('can:view-office')->name('office.show');
        Route::delete('{model}', [OfficeController::class, 'destroy'])->middleware('can:delete-office')->name('office.destroy');
    });

    Route::group(['prefix' => 'document-type'], function () {
        Route::get('', [DocumentTypeController::class, 'index'])->middleware('can:view-document-type')->name('document-type.index');
        Route::get('json-index', [DocumentTypeController::class, 'jsonAll'])->middleware('can:view-any-document-type')->name('document-type.json-index');
        Route::get('create', [DocumentTypeController::class, 'create'])->middleware('can:create-document-type')->name('document-type.create');
        Route::post('store', [DocumentTypeController::class, 'store'])->middleware('can:create-document-type')->name('document-type.store');
        Route::get('edit/{model}', [DocumentTypeController::class, 'edit'])->middleware('can:edit-document-type')->name('document-type.edit');
        Route::put('update/{model}', [DocumentTypeController::class, 'update'])->middleware('can:edit-document-type')->name('document-type.update');
        Route::get('{model}/show', [DocumentTypeController::class, 'show'])->middleware('can:view-document-type')->name('document-type.show');
        Route::delete('{model}', [DocumentTypeController::class, 'destroy'])->middleware('can:delete-document-type')->name('document-type.destroy');
    });

    Route::group(['prefix' => 'user'], function () {
        Route::get('', [UserController::class, 'index'])->middleware('can:view-user')->name('user.index');
        Route::get('json-index', [UserController::class, 'jsonAll'])->middleware('can:view-anyrole')->name('user.json-index');
        Route::get('create', [UserController::class, 'create'])->middleware('can:create-user')->name('user.create');
        Route::post('store', [UserController::class, 'store'])->middleware('can:create-user')->name('user.store');
        Route::get('edit/{model}', [UserController::class, 'edit'])->middleware('can:edit-user')->name('user.edit');
        Route::put('update/{model}', [UserController::class, 'update'])->middleware('can:edit-user')->name('user.update');
        Route::get('{model}/show', [UserController::class, 'show'])->middleware('can:view-user')->name('user.show');
        Route::delete('{model}', [UserController::class, 'destroy'])->middleware('can:delete-user')->name('user.destroy');
    });

    Route::group(['prefix' => 'role'], function () {
        Route::get('', [RoleController::class, 'index'])->middleware('can:view-anyrole')->name('role.index');
        Route::get('{model}', [RoleController::class, 'show'])->middleware('can:edit-role')->name('role.show');
        Route::put('{model}', [RoleController::class, 'update'])->middleware('can:edit-role')->name('role.update');
    });

    Route::group(['prefix' => 'mis'], function () {
        Route::get('import', [MISController::class, 'import'])->middleware('can:import-employee')->name('mis.import');
        Route::post('import-employee', [MISController::class, 'importEmployee'])->middleware('can:import-employee')->name('mis.import-employee');
        Route::get('export', [MISController::class, 'export'])->middleware('can:export-employee')->name('mis.export');
        Route::post('export-employee', [MISController::class, 'exportEmployee'])->middleware('can:export-employee')->name('mis.export-employee');
    });




});


//Employee Controller
Route::group(['middleware'=>'auth','prefix' => 'employee'], function () {
    Route::get('', [EmployeeController::class, 'indexAllEmployees'])->middleware('can:view-allemployee')->name('employees.all');
    Route::get('json-index-all', [EmployeeController::class, 'jsonAllEmployees'])->middleware('can:view-allemployee')->name('employees.json-index-all');
    Route::get('/mr', [EmployeeController::class, 'indexMrEmployees'])->middleware('can:view-allemployee')->name('employees.mr');
    Route::get('json-index-mr', [EmployeeController::class, 'jsonMrEmployees'])->middleware('can:view-allemployee')->name('employees.json-index-mr');
    Route::get('/pe', [EmployeeController::class, 'indexPeEmployees'])->middleware('can:view-allemployee')->name('employees.pe');
    Route::get('json-index-pe', [EmployeeController::class, 'jsonPeEmployees'])->middleware('can:view-allemployee')->name('employees.json-index-pe');
    Route::get('/deleted', [EmployeeController::class, 'indexDeletedEmployees'])->middleware('can:view-allemployee')->name('employees.deleted');
    Route::get('json-index-deleted', [EmployeeController::class, 'jsonDeletedEmployees'])->middleware('can:view-allemployee')->name('employees.json-index-deleted');
    Route::get('{model}/show', [EmployeeController::class, 'show'])->middleware('can:view-employee')->name('employee.show');
    Route::get('create', [EmployeeController::class, 'create'])->middleware('can:create-employee')->name('employee.create');
    Route::post('store', [EmployeeController::class, 'store'])->middleware('can:create-employee')->name('employee.store');
    Route::get('edit/{model}', [EmployeeController::class, 'edit'])->middleware('can:edit-employee')->name('employee.edit');
    Route::post('update/{model}', [EmployeeController::class, 'update'])->middleware('can:edit-employee')->name('employee.update');
});

//Transfer
Route::group(['middleware'=>'auth','prefix' => 'transfer'], function () {
    Route::post('store/{model}', [TransferController::class, 'store'])->middleware('can:transfer-employee')->name('transfer.store');
    Route::delete('{model}', [TransferController::class, 'destroy'])->middleware('can:delete-transfer')->name('transfer.destroy');
});

//Delete Employee
Route::group(['middleware'=>'auth','prefix' => 'deletion'], function () {
    Route::post('store/{model}', [DeletionController::class, 'store'])->middleware('can:delete-employee')->name('deletion.store');
    Route::post('update/{model}', [DeletionController::class, 'update'])->middleware('can:edit-delete')->name('deletion.update');
});

