<?php


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ********************Admin******************************
use App\Http\Controllers\Admin\HomeController as AdminHome;
use App\Http\Controllers\Admin\CentersController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\ComponentController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\TimeTableController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\FeesController;
use App\Http\Controllers\Admin\BackUpController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PublicationController;
use App\Http\Controllers\Admin\FinantialReportController;

use App\Http\Controllers\Admin\FeeEstamateController;


use App\Http\Controllers\Admin\PaymentHistoryController;
use App\Http\Controllers\Admin\PaymentVerificationController;
use App\Http\Controllers\Admin\CenterChargesController;


use App\Http\Controllers\Admin\ProcessController;
use App\Http\Controllers\Admin\StateTypeController;
use App\Http\Controllers\Admin\ActionTypeController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\TransitionController;
use App\Http\Controllers\Admin\ActionController;
use App\Http\Controllers\Admin\ActivityTypeController;
use App\Http\Controllers\Admin\ActivityController;





use App\Http\Controllers\Admin\ServiceController as AdminService;
use App\Http\Controllers\Admin\ServicesItemContoller;
use App\Http\Controllers\Admin\ServiceRequirementContoller;
use App\Http\Controllers\Admin\ServiceSaleContolller;
use App\Http\Controllers\Service\ServiceController;

// ********************Center******************************
use App\Http\Controllers\Admin\CandidateProfileController;
use App\Http\Controllers\Admin\CandidateRegistrationController;
use App\Http\Controllers\Admin\CandidateEntryController;

use App\Http\Controllers\Admin\DisciplineController;
use App\Http\Controllers\Admin\DocumentCategoryController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\InvigilationCandidateController;
use App\Http\Controllers\admin\InvigilationListController;
use App\Http\Controllers\Admin\InvigilationPaymentMethodController;
use App\Http\Controllers\Admin\InvigilationRoleController;
use App\Http\Controllers\Admin\InvigilationTypeController;
use App\Http\Controllers\Admin\LateFeeController;
use App\Http\Controllers\Admin\OverPrintController;
use App\Http\Controllers\Admin\SponsorUserController;
use App\Http\Controllers\Admin\SubjectOptionController;
use App\Http\Controllers\application\InvigilatorProfileController;
use App\Http\Controllers\School\HomeController as SchoolHome;
use App\Http\Controllers\School\CandidateController;
use App\Http\Controllers\School\UserController as SchoolUsers;
use App\Http\Controllers\School\ReportController;
use App\Http\Controllers\School\PaymentController;
use App\Http\Controllers\School\HelpController;
// Candidates
use App\Http\Controllers\Candidate\HomeController as Candidate;
use App\Http\Controllers\Candidate\PaymentController as CandidatePayment;
use App\Http\Controllers\Candidate\ProfileController as CandidateProfile;
use App\Http\Controllers\School\InvigilatorController;
// Sponsors
use App\Http\Controllers\Sponsor\HomeController as Sponsor;
use App\Http\Controllers\Sponsor\ProfileController as SponsorProfile;
use App\Http\Controllers\Sponsor\CandidateController as SponsorCandidate;


use App\Http\Controllers\RegistrationController;

use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Registration

Route::get('/', [RegistrationController::class, 'index']);
Route::get('/private-candidate', [RegistrationController::class, 'privateCandidate'])->name('private.cadidate');
Route::post('/private-multiform-personal', [RegistrationController::class, 'multiformPersonal'])->name('private.multiform');
// MPESA
Route::get('/test', [RegistrationController::class, 'generateSessionKey']);

Route::post('/ecocash', [RegistrationController::class, 'getEcoCashResponse'])->name('register.ecoCashResponse');
Route::get('/cache-clear', function () {
    //symlink('/home/ecol/online_registration/storage/app/public', '/home/ecol/ecol.coltech.co.za/storage');
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:cache');
    return "Done";
});




//registeration-autocomplete-search
Route::post('/registeration', [RegistrationController::class, 'register']);
Route::post('/register-candidate-subjects', [RegistrationController::class, 'candidateSubjects'])->name('registeration.candidateSubjects');
Route::post('/registeration-autocomplete-search', [RegistrationController::class, 'autocompleteSearch'])->name('registeration.autocomplete');
Route::post('/registeration-center-subjects', [RegistrationController::class, 'centersubjects'])->name('registeration.centersubjects');
Route::get('/print-timetable', [RegistrationController::class, 'print'])->name('registeration.printtimetable');
Route::post('/payment-transaction', [RegistrationController::class, 'paymentTransaction'])->name('transaction');
Route::post('/payment-balance', [RegistrationController::class, 'paymentBalance'])->name('balance');
// applications
Route::get('/applications/{token}', [InvigilatorProfileController::class, 'index'])->name('applications.index');
Route::put('/applications/update/{token}', [InvigilatorProfileController::class, 'update'])->name('applications.update');

// Services
Route::group([
    'prefix' => 'services',
    'as' => 'services.',
], function () {
    Route::get('/', [ServiceController::class, 'index']);
    Route::post('/services-item', [ServiceController::class, 'getOneTimeServicesItem'])->name('serviceItem');
    Route::post('/services-requirements', [ServiceController::class, 'serviceRequirements'])->name('serviceRequirements');
    Route::post('/search-candidate', [ServiceController::class, 'searchCandidate'])->name('search-candidate');
    Route::post('/valid-candidate', [ServiceController::class, 'validCandidate'])->name('validCandidate');
    Route::post('/check-status', [ServiceController::class, 'checkStatus'])->name('checkstatus');
    Route::post('/centers-autocomplete-search', [ServiceController::class, 'autocompleteAllCentersSearch'])->name('autocomplete');
    Route::post('/multiform', [ServiceController::class, 'multiform'])->name('multiform');
    Route::post('/payment-transaction', [ServiceController::class, 'paymentTransaction'])->name('transaction');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['guest:web', 'PreventBackHistory'])->group(function () {
        Route::get('login', [
            'as' => 'login',
            'uses' => 'App\Http\Controllers\Admin\Auth\LoginController@index'
        ]);
        Route::post('login', [
            'as' => '',
            'uses' => 'App\Http\Controllers\Admin\Auth\LoginController@login'
        ]);
        Route::post('logout', [
            'as' => 'logout',
            'uses' => 'App\Http\Controllers\Admin\Auth\LoginController@logout'
        ]);
        // Password Reset Routes
        Route::get('password/reset', 'App\Http\Controllers\Admin\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
        Route::post('password/email', 'App\Http\Controllers\Admin\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
        Route::get('password/reset/{token}', 'App\Http\Controllers\Admin\Auth\ResetPasswordController@showResetForm')->name('password.reset');
        Route::post('password/reset', 'App\Http\Controllers\Admin\Auth\ResetPasswordController@reset')->name('password.update');
    });
    Route::middleware(['auth:admin', 'PreventBackHistory'])->group(function () {
        //'middleware' => 'admin'

        Route::get('/', [AdminHome::class, 'index'])->name('home');
        Route::get('/registeredSubjects', [AdminHome::class, 'registeredSubjects'])->name('registeredsubjects');
        // ************Centers**********************
        Route::get('/centers/all', [CentersController::class, 'allCenters'])->name('centers.allCenters');
        Route::get('/centers/update-status', [CentersController::class, 'updateStatus'])->name('centers.updateStatus');
        Route::put('/centers/update-sessions/{id}', [CentersController::class, 'updateSessions'])->name('centers.updateSessions');
        Route::put('/centers/update-level/{id}', [CentersController::class, 'updateLevels'])->name('centers.updateLevels');
        Route::put('/centers/update-subjects/{id}', [CentersController::class, 'updateSubjects'])->name('centers.updateSubjects');


        // invigilation

        Route::prefix('invigilations')->name('invigilations.')->group(function () {
            // routes here
            Route::resource('types', InvigilationTypeController::class);
            Route::resource('candidatesrange', InvigilationCandidateController::class);
            Route::resource('roles', InvigilationRoleController::class);
            Route::resource('paymentmethods', InvigilationPaymentMethodController::class);
            Route::resource('contracts', InvigilationListController::class);
        });

        Route::get('/centers/reset-password/{id}', [CentersController::class, 'resetCenterPassword'])->name('centers.resetCenterPassword');
        Route::get('/centers/export-password', [CentersController::class, 'exportPassword'])->name('centers.exportpassword');
        Route::put('/centers/change-role/{center_no}', [CentersController::class, 'changeRole'])->name('centers.changerole');
        Route::resource('centers', CentersController::class);
        // ****************Users***********************
        Route::get('/createAllCenterUser', [UserController::class, 'createAllCenterUser'])->name('user.createAllCenterUser');
        Route::get('/all-users', [UserController::class, 'getAllUsers'])->name('user.allusers');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/edit/{edit}', [UserController::class, 'edit'])->name('users.edit');
        Route::get('/users/profile', [UserController::class, 'editProfile'])->name('users.editprofile');
        Route::get('/users/password', [UserController::class, 'editPassword'])->name('users.editpassword');
        Route::post('users/password', [UserController::class, 'updatePassword'])->name('users.updatepassword');
        Route::put('/users/profile/{user}', [UserController::class, 'updateProfile'])->name('users.updateprofile');
        Route::post('users/change-status', [UserController::class, 'changeUserStatus'])->name('users.changeuserstatus');
        Route::post('/users/update', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/add', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/change-password', [UserController::class, 'changePassword'])->name('users.changepassword');
        Route::resource('sponsors', SponsorUserController::class);

        // ************Roles & Permissions**********************
        Route::resource('permissions', PermissionController::class);
        Route::put('/role/{id}', [RoleController::class, 'updateRolePermission'])->name('roles.updateRolePermission');
        Route::resource('roles', RoleController::class);
        // ************Subjects*********************************
        Route::resource('subjects', SubjectController::class);
        // ************Components**************************
        Route::resource('components', ComponentController::class);
        // ************Disciplines**************************
        Route::resource('disciplines', DisciplineController::class);
        // ************Sessions************************
        Route::resource('sessions', SessionController::class);
        // ************Levels**************************
        Route::resource('levels', LevelController::class);
        // ************Options**************************
        Route::resource('options', SubjectOptionController::class);

        // transitions
        // ************Process**************************



        // ************Process**************************
        Route::resource('processes', ProcessController::class);
        // ************State Types**************************
        Route::resource('state-types', StateTypeController::class);
        Route::resource('states', StateController::class);
        // ************Activity Types**************************
        Route::resource('activity-types', ActivityTypeController::class);
        Route::resource('activities', ActivityController::class);


        // ************Action Types**************************

        Route::resource('action-types', ActionTypeController::class);
        Route::get('/actions-order', [ActionController::class, 'approvalOrder'])->name('actions.order');
        Route::resource('actions', ActionController::class);
        // ************Transitions**************************
        Route::resource('transitions', TransitionController::class);
        // ************Candidate Profiles************************


        Route::post('/candidate-profile/update-candidate-number', [CandidateProfileController::class, 'updateCandidateNumber'])->name('candidate-profile.updateCandidateNumber');
        Route::post('/candidate-profile/import', [CandidateProfileController::class, 'import'])->name('candidate-profile.import');
        Route::resource('candidate-profile', CandidateProfileController::class);
        // ************Candidate registratin*********************
        // searchCanididate
        Route::post('/candidate-registation/center-subjects', [CandidateController::class, 'centersubjects'])->name('candidates.center_subjects');
        Route::post('/candidate-registation/search', [CandidateRegistrationController::class, 'searchCandidate'])->name('candidate-registation.search');
        Route::get('/candidate-registation/export-candidates', [CandidateRegistrationController::class, 'exportCandidatesRegistration'])->name('candidate-registation.registration');
        Route::get('/candidate-registation/export-amendments', [CandidateRegistrationController::class, 'exportAmendmentList'])->name('candidate-registation.amendments');
        Route::resource('candidate-registation', CandidateRegistrationController::class);
        // ************Candidate Entries*********************
        Route::get('/candidates-entries', [CandidateEntryController::class, 'index'])->name('candidates.entries.index');
        Route::post('/candidates-entries', [CandidateEntryController::class, 'export'])->name('candidates.entries.export');
        Route::get('/candidates-entries/autocompleteSearchCenter', [CandidateEntryController::class,'autocompleteSearchCenter'])->name('candidates.entries.autocompleteSearchCenter');
        Route::get('/candidates-entries/autocompleteSearchSubject', [CandidateEntryController::class,'autocompleteSearchSubject'])->name('candidates.entries.autocompleteSearchSubject');
        // ************Documents**********************
        Route::prefix('document-categories')->name('document-categories.')->group(function () {
            Route::get('/', [DocumentCategoryController::class, 'index'])->name('index');
            Route::post('/', [DocumentCategoryController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [DocumentCategoryController::class, 'edit'])->name('edit');
            Route::put('/{id}', [DocumentCategoryController::class, 'update'])->name('update');
            Route::delete('/{id}', [DocumentCategoryController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [DocumentController::class, 'index'])->name('index');
            Route::get('/roles-users', [DocumentController::class, 'getRoleUser'])->name('getRoleUser');
            Route::post('/', [DocumentController::class, 'store'])->name('store');
        });



        Route::get('/candidates-entries/autocompleteSearchCenter', [CandidateEntryController::class, 'autocompleteSearchCenter'])->name('candidates.entries.autocompleteSearchCenter');





        // ************TimeTables*********************************
        Route::get('/timetable', [TimeTableController::class, 'index'])->name('timetable.index');
        Route::post('/timetable-update', [TimeTableController::class, 'update'])->name('timetable.update');
        // certificate
        Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
        Route::post('/certificates', [CertificateController::class, 'print'])->name('certificates.print');

        // ****************Over Print**********************
        Route::get('/over-print', [OverPrintController::class, 'index'])->name('over-print.index');
        Route::post('/over-print', [OverPrintController::class, 'print'])->name('over-print.print');


        // ****************Fees Charges**********************
        Route::resource('fees', FeesController::class);
        Route::resource('late-fees', LateFeeController::class);

        // ****************Payment History *******************
        Route::get('/payment-history-invoice', [PaymentHistoryController::class, 'index'])->name('payment-history.index');
        Route::get('/payment-history-payment', [PaymentHistoryController::class, 'paymentHistory'])->name('payment-history.payments');
        // ****************Payment Verification *******************
        // centers
        Route::get('/payments-verification', [PaymentVerificationController::class, 'index'])->name('payments-verification.index');
        Route::get('/payments-verification-center/{center_no}', [PaymentVerificationController::class, 'center_proof_payments'])->name('payments-verification.center');
        Route::get('/verification-center-edit/{id}', [PaymentVerificationController::class, 'editProofPaymentCenter'])->name('payments-verification.center.edit');
        Route::put('/verification-center-update/{id}', [PaymentVerificationController::class, 'updateProofPaymentCenter'])->name('payments-verification.center.update');
        Route::delete('/verification-center-delete/{id}', [PaymentVerificationController::class, 'deleteProofPaymentCenter'])->name('payments-verification.center.delete');
        Route::post('/center-charges/{id}', [PaymentVerificationController::class, 'centerCharges'])->name('payments-verification.centercharges');
        // candidates

        Route::post('/payments-verification-candidate-search', [PaymentVerificationController::class, 'searchCandidate'])->name('payments-verification.searchcandidate');
        Route::post('/payments-verification-store', [PaymentVerificationController::class, 'storeCandidate'])->name('payments-verification.storecandidate');


        Route::get('/payments-verification-candidates', [PaymentVerificationController::class, 'privateCandidates'])->name('payments-verification.privatecandidates');
        Route::get('/verification-candidate-edit/{id}', [PaymentVerificationController::class, 'editProofPaymentCandidate'])->name('payments-verification.candidate.edit');
        Route::put('/verification-candidate-update/{id}', [PaymentVerificationController::class, 'updateProofPaymentCandidate'])->name('payments-verification.candidate.update');
        Route::delete('/verification-candidate-delete/{id}', [PaymentVerificationController::class, 'deleteProofPaymentCandidate'])->name('payments-verification.candidate.delete');
        Route::delete('/verification-candidate-delete/{id}', [PaymentVerificationController::class, 'deleteProofPaymentCandidate'])->name('payments-verification.candidate.delete');
        Route::get('/candidate-confirmation-remove-image', [PaymentVerificationController::class, 'removeImage'])->name('payments-verification.candidate.remove-image');
        // comments
        Route::get('/candidate-confirmation-comments-edit/{id}', [PaymentVerificationController::class, 'editComments'])->name('payments-verification.comments.edit');
        Route::put('/candidate-confirmation-comments-edit/{id}', [PaymentVerificationController::class, 'updateComments'])->name('payments-verification.comments.update');
        // Balance Brought Forward
        Route::post('/balance-bforward', [PaymentVerificationController::class, 'addBalanceBroughtForward'])->name('payments-verification.balanceBroughtForward');

        // ****************Fee Estamates *******************
        Route::get('/fee-estamates', [FeeEstamateController::class, 'index'])->name('fee-estamates.index');
        Route::get('/fee-estamates-private-centers', [FeeEstamateController::class, 'privateCenters'])->name('fee-estamates.privatecenters');


        // ******************Reports *******************
        Route::get('/finantial-report', [FinantialReportController::class, 'index'])->name('finantial-report.index');
        Route::get('/finantial-report/download', [FinantialReportController::class, 'report'])->name('finantial-report.report');



        // ******************Services *******************
        Route::resource('services', AdminService::class);
        Route::resource('service-item', ServicesItemContoller::class);
        Route::resource('service-requirements', ServiceRequirementContoller::class);
        Route::get('/service-sales/comments/{id}', [ServiceSaleContolller::class, 'editComments'])->name('service-sales.editcomments');
        Route::put('/service-sales/comments/{id}', [ServiceSaleContolller::class, 'updateComments'])->name('service-sales.updatecomments');
        Route::resource('service-sales', ServiceSaleContolller::class);


        // ******************Center charges *******************
        Route::resource('center-charges', CenterChargesController::class);

        // ******************Logs*********************
        Route::get('/user-activity', [LogController::class, 'index'])->name('logs.index');
        Route::post('/logs', [LogController::class, 'setActitiesLogs'])->name('logs.setActitiesLogs');


        // ******************Setting *********************
        Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::put('/setting/{id}', [SettingController::class, 'update'])->name('setting.update');


        // *************Publications Publication ************
        Route::get('/publications', [PublicationController::class, 'index'])->name('publications.index');
        Route::get('/publications-display', [PublicationController::class, 'displayPublications'])->name('publications.display');
        Route::post('/publications', [PublicationController::class, 'update'])->name('publications.update');

        // ****************BackUp *******************
        Route::get('/backup', [BackUpController::class, 'index'])->name('backup.index');
        Route::get('/create', [BackUpController::class, 'create'])->name('backup.create');
        Route::get('/download/{file_name}', [BackUpController::class, 'download'])->name('backup.download');
        Route::get('/delete/{file_name}', [BackUpController::class, 'delete'])->name('backup.delete');
        Route::post('/backup', [BackUpController::class, 'restore'])->name('backup.restore');
    });
});
Route::group(['prefix' => 'center', 'as' => 'center.',], function () {
    Route::middleware(['guest:web', 'PreventBackHistory'])->group(function () {
        Route::get('login', [
            'as' => 'login',
            'uses' => 'App\Http\Controllers\School\Auth\LoginController@index'
        ]);
        Route::post('login', [
            'as' => '',
            'uses' => 'App\Http\Controllers\School\Auth\LoginController@login'
        ]);
    });

    // Centers Routes
    Route::middleware(['auth:web', 'PreventBackHistory'])->group(function () {
        Route::post('logout', [
            'as' => 'logout',
            'uses' => 'App\Http\Controllers\School\Auth\LoginController@logout'
        ]);

        // Home    'middleware' => 'center'
        Route::get('/', [SchoolHome::class, 'index'])->name('home');

        // Center
        Route::prefix('invigilators')->name('invigilators.')->group(function () {
            // routes here
            Route::resource('invigilators', InvigilatorController::class);
        });
        Route::get('/invigilators', [InvigilatorController::class, 'index'])->name('invigilators.index');
        Route::get('/invigilators/edit/{id}', [InvigilatorController::class, 'edit'])->name('invigilators.edit');
        Route::post('/invigilators/store', [InvigilatorController::class, 'store'])->name('invigilators.store');
        Route::put('/invigilators/update/{id}', [InvigilatorController::class, 'update'])->name('invigilators.update');
        Route::delete('/invigilators/delete/{id}', [InvigilatorController::class, 'destroy'])->name('invigilators.destroy');

        // Candidates
        Route::get('/candidates', [CandidateController::class, 'index'])->name('candidates.index');
        Route::get('/fatchamendments', [CandidateController::class, 'fatchAmendments'])->name('candidates.fatchAmendments');

        Route::get('/candidate-show/{id}', [CandidateController::class, 'showCandidate'])->name('candidates.showCandidate');
        Route::get('/candidates/edit/{id}', [CandidateController::class, 'editCandidate'])->name('candidates.edit');
        Route::put('/candidates/update/{id}', [CandidateController::class, 'updateCandidate'])->name('candidates.update');

        Route::get('/editcandidateDOB/{id}', [CandidateController::class, 'editCandidateDOB'])->name('candidates.editCandidateDOB');
        Route::put('/updatecandidateDOB/{id}', [CandidateController::class, 'updateCandidateDOB'])->name('candidates.updateCandidateDOB');


        Route::post('/candidate/search', [CandidateController::class, 'searchCandidate'])->name('candidates.search');
        Route::post('/addCandidate', [CandidateController::class, 'store'])->name('candidates.store');

        Route::post('/center-subjects', [CandidateController::class, 'centersubjects'])->name('candidates.center_subjects');



        Route::delete('/delete-candidate/{candidateNo}', [CandidateController::class, 'deleteCandidate']);

        Route::post('/delete-selected', [CandidateController::class, 'deleteCandidates'])->name('candidates.deleteCandidates');
        // Registered Candidates
        Route::post('/registered-candidates', [CandidateController::class, 'registered'])->name('registered');
        Route::post('/import-candidates', [CandidateController::class, 'importCandidatate'])->name('registration.importCandidatate');
        // users
        Route::get('/users', [SchoolUsers::class, 'index'])->name('users.index');
        Route::get('/allusers', [SchoolUsers::class, 'getAllUsers'])->name('users.getallusers');
        Route::post('/change-status', [SchoolUsers::class, 'changeUserStatus'])->name('users.changeuserstatus');
        Route::post('/adduser', [SchoolUsers::class, 'addUser'])->name('users.store');
        Route::get('/getuser/{username}', [SchoolUsers::class, 'getUserByUserName'])->name('users.getuser');
        Route::post('/updateUser', [SchoolUsers::class, 'updateUser'])->name('users.updateuser');
        // Profile
        Route::get('/users/profile', [SchoolUsers::class, 'editProfile'])->name('users.editprofile');
        Route::put('/users/profile/{user}', [SchoolUsers::class, 'updateProfile'])->name('users.updateprofile');


        Route::get('/users/settings', [SchoolUsers::class, 'settings'])->name('users.settings');
        Route::post('/users/change-password', [SchoolUsers::class, 'changePassword'])->name('users.changePassword');
        Route::post('users/password', [SchoolUsers::class, 'updatePassword'])->name('users.updatepassword');


        //Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/print-sponsor', [ReportController::class, 'sponsorReport'])->name('reports.printSponsorReport');
        Route::get('/print-entry-list', [ReportController::class, 'entryList'])->name('reports.printEntryList');
        Route::get('/print-entry-list-private', [ReportController::class, 'entryListPrivate'])->name('reports.printEntryListPrivate');
        Route::get('/print-timetable', [ReportController::class, 'printTimatable'])->name('reports.printtimetable');
        //Help
        Route::get('/help', [HelpController::class, 'index'])->name('help.index');

        //Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payment-candidates', [PaymentController::class, 'candidates'])->name('payment.candidates');
        Route::get('/payment-statement', [PaymentController::class, 'payment']);
        Route::post('/make-payment', [PaymentController::class, 'makePayement'])->name('payments.makepayment');
        Route::post('/upload-bank-stament', [PaymentController::class, 'uploadBankStament'])->name('payments.uploadBankStament');
    });
});


Route::group(['prefix' => 'candidate', 'as' => 'candidate.',], function () {
    Route::middleware(['guest:candidate', 'PreventBackHistory'])->group(function () {
        Route::get('login', [
            'as' => 'login',
            'uses' => 'App\Http\Controllers\Candidate\Auth\LoginController@index'
        ]);
        Route::post('login', [
            'as' => '',
            'uses' => 'App\Http\Controllers\Candidate\Auth\LoginController@login'
        ]);
    });
    Route::middleware(['auth:candidate', 'PreventBackHistory'])->group(function () {
        Route::post('logout', [
            'as' => 'logout',
            'uses' => 'App\Http\Controllers\Candidate\Auth\LoginController@logout'
        ]);
        Route::get('/', [Candidate::class, 'index'])->name('home');
        // Candidates /CandidateProfile
        Route::get('/payment', [CandidatePayment::class, 'index'])->name('payment');
        Route::post('/payment-transaction', [CandidatePayment::class, 'paymentTransaction'])->name('transaction');

        Route::get('/print-timetable', [Candidate::class, 'print'])->name('candidate.timetable');


        Route::get('/profile', [CandidateProfile::class, 'index'])->name('profile.index');
        Route::get('/profile-next-kin', [CandidateProfile::class, 'showNextOfKin'])->name('profile.kin');
        Route::get('/profile-show', [CandidateProfile::class, 'showCandidateInfo'])->name('profile.show');
        Route::post('/profile-update', [CandidateProfile::class, 'multiformPersonal'])->name('profile.update');
    });
});


Route::group(['prefix' => 'sponsor', 'as' => 'sponsor.',], function () {
    Route::middleware(['guest:sponsor', 'PreventBackHistory'])->group(function () {
        Route::post('login', [
            'as' => '',
            'uses' => 'App\Http\Controllers\Sponsor\Auth\LoginController@login'
        ]);
        Route::middleware(['guest:sponsor', 'PreventBackHistory'])->group(function () {
            Route::get('login', [
                'as' => 'login',
                'uses' => 'App\Http\Controllers\Sponsor\Auth\LoginController@index'
            ]);
            Route::post('logout', [
                'as' => 'logout',
                'uses' => 'App\Http\Controllers\Sponsor\Auth\LoginController@logout'
            ]);
            // Password Reset Routes
            Route::get('password/reset', 'App\Http\Controllers\Sponsor\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
            Route::post('password/email', 'App\Http\Controllers\Sponsor\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
            Route::get('password/reset/{token}', 'App\Http\Controllers\Sponsor\Auth\ResetPasswordController@showResetForm')->name('password.reset');
            Route::post('password/reset', 'App\Http\Controllers\Sponsor\Auth\ResetPasswordController@reset')->name('password.update');
        });
    });
    Route::middleware(['auth:sponsor', 'PreventBackHistory'])->group(function () {
        Route::post('logout', [
            'as' => 'logout',
            'uses' => 'App\Http\Controllers\Sponsor\Auth\LoginController@logout'
        ]);
        Route::get('/', [Sponsor::class, 'index'])->name('home');
        Route::get('/candidate', [SponsorCandidate::class, 'index'])->name('candidate.index');
        Route::post('/center-level', [SponsorCandidate::class, 'centers'])->name('candidate.centers');
        Route::get('/profile', [SponsorProfile::class, 'index'])->name('profile.index');
        //Approve
        Route::get('/candidate/{id}/edit', [SponsorCandidate::class, 'edit'])->name('candidate.edit');
        Route::put('/candidate/{id}', [SponsorCandidate::class, 'update'])->name('candidate.update');
    });
});
