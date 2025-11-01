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
use App\Http\Controllers\EditRequestController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\DeletionController;
use App\Http\Controllers\RemunerationController;
use App\Http\Controllers\EngagementCardController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SchemeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BioController;




Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

//Pages
Route::group(['prefix'=>'page'], function () {
    Route::get('contact',[PageController::class,'contact'])->name('page.contact');
    Route::get('privacy',[PageController::class,'privacy'])->name('page.privacy');
    Route::get('term',[PageController::class,'term'])->name('page.term');

});

//Bio
Route::group(['prefix'=>'bio'], function () {
    Route::get('index',[BioController::class,'index'])->name('bio.index');
    Route::post('send-otp', [BioController::class, 'sendOtp'])->name('bio.send');
    Route::post('verify-otp', [BioController::class, 'verifyOtp'])->name('bio.verify');
    Route::get('show/{employee:mobile}', [BioController::class, 'show'])->name('bio.show');
    Route::get('/download/{model}', [BioController::class, 'downloadEngagementCard'])->name('bio.download-engagement-card');
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
//    Route::get('admin',[DashboardController::class,'admin'])->name('dashboard.admin');
//    Route::get('manager',[DashboardController::class,'manager'])->name('dashboard.manager');
    Route::get('stats/office', [DashboardController::class, 'officeStatistics'])->name('dashboard.office-statistics');
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

    Route::group(['prefix' => 'schemes'], function () {
        Route::get('', [SchemeController::class, 'index'])->middleware('can:view-scheme')->name('scheme.index');
        Route::get('create', [SchemeController::class, 'create'])->middleware('can:create-scheme')->name('scheme.create');
        Route::post('store', [SchemeController::class, 'store'])->middleware('can:create-scheme')->name('scheme.store');
        Route::get('edit/{model}', [SchemeController::class, 'edit'])->middleware('can:edit-scheme')->name('scheme.edit');
        Route::put('update/{model}', [SchemeController::class, 'update'])->middleware('can:edit-scheme')->name('scheme.update');
        Route::delete('{model}', [SchemeController::class, 'destroy'])->middleware('can:delete-scheme')->name('scheme.destroy');
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


        Route::get('import-work-charge', [MISController::class, 'importWorkCharge'])->middleware('can:import-employee')->name('mis.import-work-charge');
        Route::post('import-work-charge-employee', [MISController::class, 'importWorkChargeEmployee'])->middleware('can:import-employee')->name('mis.import-work-charge-employee');

        Route::get('import-engagement', [MISController::class, 'importDateOfEngagement'])->middleware('can:import-employee')->name('mis.import-engagement');
        Route::post('import-engagement-employee', [MISController::class, 'importDateOfEngagementEmployee'])->middleware('can:import-employee')->name('mis.import-engagement-employee');

        Route::get('export', [MISController::class, 'export'])->middleware('can:export-employee')->name('mis.export');
        Route::post('export-employee', [MISController::class, 'exportEmployee'])->middleware('can:export-employee')->name('mis.export-employee');

        Route::get('create-wc-employee', [MISController::class, 'createWC'])->middleware('can:create-employee')->name('mis.create-wc-employee');
        Route::get('create-pe-employee', [MISController::class, 'createPE'])->middleware('can:create-employee')->name('mis.create-pe-employee');
        Route::get('create-mr-employee', [MISController::class, 'createMR'])->middleware('can:create-employee')->name('mis.create-mr-employee');

        Route::get('engagement-card', [MISController::class, 'engagementCard'])->middleware('can:generate-engagement-card')->name('mis.engagement-card');
        Route::get('json-engagement-card', [MISController::class, 'jsonEngagementCard'])->middleware('can:generate-engagement-card')->name('mis.json-engagement-card');


        Route::get('employee-document', [MISController::class, 'employeeDocument'])->middleware('can:update-document')->name('mis.employee-document');
        Route::get('json-employee-document', [MISController::class, 'jsonEmployeeDocument'])->middleware('can:update-document')->name('mis.json-employee-document');


    });

    Route::group(['prefix' => 'remuneration'], function () {

        Route::get('details', [RemunerationController::class, 'remunerationDetail'])->middleware('can:generate-remuneration')->name('remuneration.detail');
        Route::get('json-remuneration-details', [RemunerationController::class, 'jsonRemunerationDetail'])->middleware('can:generate-remuneration')->name('remuneration.json-detail');


        Route::get('summary', [RemunerationController::class, 'remunerationSummary'])->middleware('can:view-remuneration')->name('remuneration.summary');
        Route::get('json-remuneration-summary', [RemunerationController::class, 'jsonRemunerationSummary'])->middleware('can:view-remuneration')->name('remuneration.json-summary');

        Route::post('store/{model}', [RemunerationController::class, 'store'])->middleware('can:create-remuneration')->name('remuneration.store');
        Route::put('update/{model}', [RemunerationController::class, 'update'])->middleware('can:edit-remuneration')->name('remuneration.update');
        Route::post('bulk-update', [RemunerationController::class, 'bulkUpdate'])->middleware('can:bulk-update-remuneration')->name('remuneration.bulk-update');
    });

});


//Manager MIS
Route::group(['prefix' => 'manager','middleware'=>'auth'], function () {

    Route::group(['prefix' => 'mis'], function () {


        Route::get('remuneration', [MISController::class, 'managerRemuneration'])->middleware('can:view-remuneration')->name('mis.manager-remuneration');
        Route::get('json-remuneration', [MISController::class, 'jsonManagerRemuneration'])->middleware('can:view-remuneration')->name('mis.manager-json-remuneration');


        Route::get('engagement-card', [MISController::class, 'managerEngagementCard'])->middleware('can:download-engagement-card')->name('mis.manager-engagement-card');
        Route::get('json-engagement-card', [MISController::class, 'jsonManagerEngagementCard'])->middleware('can:download-engagement-card')->name('mis.manager-json-engagement-card');



    });

});


//Employee Controller
Route::group(['middleware'=>'auth','prefix' => 'employees'], function () {
    Route::get('all', [EmployeeController::class, 'allEmployees'])->middleware('can:view-allemployee')->name('employees.all');
    Route::get('index-all/{model}', [EmployeeController::class, 'indexAllEmployees'])->middleware('can:view-allemployee')->name('employees.index-all');
    Route::get('json-index-all/{model}', [EmployeeController::class, 'jsonAllEmployees'])->middleware('can:view-allemployee')->name('employees.json-index-all');


    Route::get('wc', [EmployeeController::class, 'wcEmployees'])->middleware('can:view-wc-employee')->name('employees.wc');
    Route::get('index-wc/{model}', [EmployeeController::class, 'indexWcEmployees'])->middleware('can:view-wc-employee')->name('employees.index-wc');
    Route::get('json-index-wc/{model}', [EmployeeController::class, 'jsonWcEmployees'])->middleware('can:view-wc-employee')->name('employees.json-index-wc');

    Route::get('pe', [EmployeeController::class, 'peEmployees'])->middleware('can:view-pe-employee')->name('employees.pe');
    Route::get('index-pe/{model}', [EmployeeController::class, 'indexPeEmployees'])->middleware('can:view-pe-employee')->name('employees.index-pe');
    Route::get('json-index-pe/{model}', [EmployeeController::class, 'jsonPeEmployees'])->middleware('can:view-pe-employee')->name('employees.json-index-pe');

    Route::get('mr', [EmployeeController::class, 'mrEmployees'])->middleware('can:view-mr-employee')->name('employees.mr');
    Route::get('index-mr/{model}', [EmployeeController::class, 'indexMrEmployees'])->middleware('can:view-mr-employee')->name('employees.index-mr');
    Route::get('json-index-mr/{model}', [EmployeeController::class, 'jsonMrEmployees'])->middleware('can:view-mr-employee')->name('employees.json-index-mr');


    Route::get('scheme', [EmployeeController::class, 'schemeEmployees'])->middleware('can:view-scheme-employee')->name('employees.scheme');
    Route::get('index-scheme/{model}', [EmployeeController::class, 'indexSchemeEmployees'])->middleware('can:view-scheme-employee')->name('employees.index-scheme');
    Route::get('json-index-scheme/{model}', [EmployeeController::class, 'jsonSchemeEmployees'])->middleware('can:view-scheme-employee')->name('employees.json-index-scheme');

    Route::get('/deleted', [EmployeeController::class, 'deletedEmployees'])->middleware('can:view-deleted-employee')->name('employees.deleted');
    Route::get('json-index-deleted', [EmployeeController::class, 'jsonDeletedEmployees'])->middleware('can:view-deleted-employee')->name('employees.json-index-deleted');


    Route::get('manager-all', [EmployeeController::class, 'managerAll'])->middleware('can:view-allemployee')->name('employees.manager.all');
    Route::get('json-manager-all', [EmployeeController::class, 'jsonMangerAll'])->middleware('can:view-allemployee')->name('employees.json-manager-all');


    Route::get('manager-wc', [EmployeeController::class, 'managerWc'])->middleware('can:view-wc-employee')->name('employees.manager.wc');
    Route::get('json-manager-wc', [EmployeeController::class, 'jsonMangerWc'])->middleware('can:view-wc-employee')->name('employees.json-manager-wc');

    Route::get('manager-pe', [EmployeeController::class, 'managerPe'])->middleware('can:view-pe-employee')->name('employees.manager.pe');
    Route::get('json-manager-pe', [EmployeeController::class, 'jsonMangerPe'])->middleware('can:view-pe-employee')->name('employees.json-manager-pe');

    Route::get('manager-mr', [EmployeeController::class, 'managerMr'])->middleware('can:view-mr-employee')->name('employees.manager.mr');
    Route::get('json-manager-mr', [EmployeeController::class, 'jsonMangerMr'])->middleware('can:view-mr-employee')->name('employees.json-manager-mr');


    Route::get('manager-scheme', [EmployeeController::class, 'managerScheme'])->middleware('can:view-scheme-employee')->name('employees.manager.scheme');
    Route::get('json-manager-scheme', [EmployeeController::class, 'jsonMangerScheme'])->middleware('can:view-scheme-employee')->name('employees.json-manager-scheme');


    Route::get('manager-deleted', [EmployeeController::class, 'managerDeleted'])->middleware('can:view-deleted-employee')->name('employees.manager.deleted');
    Route::get('json-manager-deleted', [EmployeeController::class, 'jsonMangerDeleted'])->middleware('can:view-deleted-employee')->name('employees.json-manager-deleted');

    Route::get('/trashed', [EmployeeController::class, 'trashedEmployees'])->middleware('can:view-trashed-employee')->name('employees.trashed');
    Route::get('json-index-trashed', [EmployeeController::class, 'jsonTrashedEmployees'])->middleware('can:view-trashed-employee')->name('employees.json-index-trashed');

    Route::get('/master', [EmployeeController::class, 'masterEmployees'])->middleware('can:view-master-employee')->name('employees.master');
    Route::get('json-index-master', [EmployeeController::class, 'jsonMasterEmployees'])->middleware('can:view-master-employee')->name('employees.json-index-master');


    Route::get('{model}/show', [EmployeeController::class, 'show'])->middleware('can:view-employee')->name('employee.show');
    Route::get('create', [EmployeeController::class, 'create'])->middleware('can:create-employee')->name('employee.create');
    Route::post('store', [EmployeeController::class, 'store'])->middleware('can:create-employee')->name('employee.store');
    Route::get('edit/{model}', [EmployeeController::class, 'edit'])->middleware('can:edit-employee')->name('employee.edit');
    Route::post('update/{model}', [EmployeeController::class, 'update'])->middleware('can:edit-employee')->name('employee.update');
    Route::delete('/delete/{model}', [EmployeeController::class, 'destroy'])->middleware('can:delete-employee')->name('employee.destroy');
    Route::put('/restore/{model}', [EmployeeController::class, 'restore'])->middleware('can:delete-employee')->name('employee.restore');
    Route::delete('/force-delete/{id}', [EmployeeController::class, 'destroyPermanently'])->middleware('can:delete-employee')->name('employees.forceDelete');
});


//Edit Request
Route::group(['middleware'=>'auth','prefix' => 'edit'], function () {
    Route::post('request/{model}', [EditRequestController::class, 'request'])->middleware('can:request-edit')->name('edit.request');
    Route::post('approve/{model}', [EditRequestController::class, 'approve'])->middleware('can:approve-edit')->name('edit.approve');
    Route::post('reject/{model}', [EditRequestController::class, 'reject'])->middleware('can:approve-edit')->name('edit.reject');
});

//Document Edit Request
Route::group(['middleware'=>'auth','prefix' => 'document'], function () {
    Route::post('request/{model}', [DocumentController::class, 'request'])->middleware('can:request-document-edit')->name('document.request');
    Route::post('approve/{model}', [DocumentController::class, 'approve'])->middleware('can:approve-document-edit')->name('document.approve');
    Route::post('reject/{model}', [DocumentController::class, 'reject'])->middleware('can:approve-document-edit')->name('document.reject');
    Route::post('update/{model}', [DocumentController::class, 'updateEmployeeDocument'])->middleware('can:update-document')->name('document.update');
    Route::delete('{model}', [DocumentController::class, 'deleteEmployeeDocument'])->middleware('can:delete-document')->name('document.destroy');

    Route::post('{model}/request-delete', [DocumentController::class, 'requestDelete'])->name('document.requestDelete');
    Route::post('delete/{model}/approve', [DocumentController::class, 'approveDelete'])->name('document.approveDelete');
    Route::post('delete/{model}/reject', [DocumentController::class, 'rejectDelete'])->name('document.rejectDelete');

    //View and Download Private Document
    Route::get('/{employee}/{document}/view', [DocumentController::class, 'viewDocument'])
        ->name('employees.documents.view');

    Route::get('/{employee}/{document}/download', [DocumentController::class, 'downloadDocument'])
        ->name('employees.documents.download');

});


//Transfer Employee
Route::group(['middleware'=>'auth','prefix' => 'transfer'], function () {
    Route::post('store/{model}', [TransferController::class, 'store'])->middleware('can:transfer-employee')->name('transfer.store');
    Route::post('request/{model}', [TransferController::class, 'request'])->middleware('can:request-transfer')->name('transfer.request');
    Route::post('approve/{model}', [TransferController::class, 'approve'])->middleware('can:approve-transfer')->name('transfer.approve');
    Route::post('reject/{model}', [TransferController::class, 'reject'])->name('transfer.reject');
    Route::delete('{model}', [TransferController::class, 'destroy'])->middleware('can:delete-transfer')->name('transfer.destroy');
});

//Delete Employee
Route::group(['middleware'=>'auth','prefix' => 'deletion'], function () {
    Route::post('store/{model}', [DeletionController::class, 'store'])->middleware('can:delete-employee')->name('deletion.store');
    Route::post('update/{model}', [DeletionController::class, 'update'])->middleware('can:edit-delete')->name('deletion.update');
    Route::post('request/{model}', [DeletionController::class, 'request'])->middleware('can:request-delete')->name('deletion.request');
    Route::post('approve/{model}', [DeletionController::class, 'approve'])->middleware('can:approve-delete')->name('deletion.approve');
    Route::post('reject/{model}', [DeletionController::class, 'reject'])->middleware('can:approve-delete')->name('deletion.reject');
});

//Notifications
Route::group(['middleware'=>'auth','prefix'=>'notification'], function () {
    Route::get('list', [NotificationController::class, 'myNotifications'])->name('notification.list');
    Route::get('/count', [NotificationController::class, 'count'])->name('notifications.count');

    //    Route::get('index', [NotificationMessageController::class, 'index'])->name('notification.index');
//    Route::get('create', [NotificationMessageController::class, 'create'])->name('notification.create');
//    Route::get('{model}/show', [NotificationMessageController::class, 'show'])->name('notification.show');
//    Route::delete('{model}', [NotificationMessageController::class, 'destroy'])->name('notification.destroy');
//    Route::post('store', [NotificationMessageController::class, 'store'])->name('notification.store');
//    Route::post('token/upload', [FcmTokenController::class, 'updateToken'])->name('token.upload');
});

//Engagement Card
Route::group(['middleware'=>'auth','prefix' => 'engagement-card'], function () {
    Route::post('update/{model}', [EngagementCardController::class, 'update'])->middleware('can:store-engagement-card')->name('engagement-card.update');
    Route::post('generate', [EngagementCardController::class, 'generate'])->middleware('can:generate-engagement-card')->name('engagement-card.generate');
    Route::post('/bulk-generate', [EngagementCardController::class, 'bulkGenerate'])->name('engagement-card.bulk-generate');
    Route::get('/download/{model}', [EngagementCardController::class, 'download'])->middleware('can:download-engagement-card')->name('engagement-card.download');
    Route::post('/engagement-card/bulk-download', [EngagementCardController::class, 'bulkDownload'])->name('engagement-card.bulk-download');
    Route::delete('{model}', [EngagementCardController::class, 'destroy'])->middleware('can:delete-engagement-card')->name('engagement-card.destroy');
});

//Summary
Route::group(['middleware'=>'auth','prefix' => 'summary'], function () {
    Route::get('work-charge', [SummaryController::class, 'workChargeSummary'])->middleware('can:view-wc-summary')->name('summary.wc');
    Route::get('provisional', [SummaryController::class, 'provisionalSummary'])->middleware('can:view-pe-summary')->name('summary.pe');
    Route::get('muster-roll', [SummaryController::class, 'musterRollSummary'])->middleware('can:view-mr-summary')->name('summary.mr');

});

//Export
Route::group(['middleware'=>'auth','prefix' => 'export'], function () {

    Route::get('summary-work-charge', [ExportController::class, 'exportWorkChargeSummary'])->middleware('can:export-wc-summary')->name('export.summary-wc');
    Route::get('summary-provisional', [ExportController::class, 'exportProvisionalSummary'])->middleware('can:export-pe-summary')->name('export.summary-pe');
    Route::get('summary-muster-roll', [ExportController::class, 'exportMusterRollSummary'])->middleware('can:export-mr-summary')->name('export.summary-mr');

    Route::get('all/{model}', [ExportController::class, 'exportAll'])->middleware('can:export-all')->name('export.all');
    Route::get('work-charge/{model}', [ExportController::class, 'exportWorkCharge'])->middleware('can:export-wc')->name('export.wc');
    Route::get('provisional/{model}', [ExportController::class, 'exportProvisional'])->middleware('can:export-pe')->name('export.pe');
    Route::get('muster-roll/{model}', [ExportController::class, 'exportMusterRoll'])->middleware('can:export-mr')->name('export.mr');
    Route::get('deleted', [ExportController::class, 'exportDeleted'])->middleware('can:export-deleted')->name('export.deleted');

    Route::get('scheme/{model}', [ExportController::class, 'exportScheme'])->middleware('can:export-scheme')->name('export.scheme');
    Route::get('master', [ExportController::class, 'exportMaster'])->middleware('can:export-master')->name('export.master');

    Route::get('remuneration-summary', [ExportController::class, 'exportRemunerationSummary'])->middleware('can:export-remuneration-summary')->name('export.remuneration-summary');

    Route::get('office-remuneration', [ExportController::class, 'exportOfficeRemuneration'])->middleware('can:export-remuneration-summary')->name('export.office-remuneration');
});
