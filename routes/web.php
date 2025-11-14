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

//Workflow
use App\Http\Controllers\Admin\WorkflowController;
use App\Http\Controllers\Admin\WorkflowInstanceController;
use App\Http\Controllers\Admin\ApprovalController;

// ********************Center******************************
use App\Http\Controllers\Admin\CandidateProfileController;
use App\Http\Controllers\Admin\CandidateRegistrationController;
use App\Http\Controllers\Admin\CandidateEntryController;
use App\Http\Controllers\Admin\CentreCollectionController;
use App\Http\Controllers\Admin\DisciplineController;

use App\Http\Controllers\Admin\LateFeeController;
use App\Http\Controllers\Admin\OverPrintController;
use App\Http\Controllers\Admin\SponsorUserController;
use App\Http\Controllers\Admin\SubjectOptionController;

use App\Http\Controllers\School\HomeController as SchoolHome;
use App\Http\Controllers\School\CandidateController;
use App\Http\Controllers\School\UserController as SchoolUsers;
use App\Http\Controllers\School\ReportController;
use App\Http\Controllers\School\PaymentController;
use App\Http\Controllers\School\HelpController;



use App\Http\Controllers\Admin\DocumentCategoryController;
use App\Http\Controllers\Admin\DocumentCommentController;
use App\Http\Controllers\School\DocumentController as SchoolDocumentController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\DocumentPermissionController;
use App\Http\Controllers\Admin\DocumentPermissionControllerMulti;
use App\Http\Controllers\Admin\DocumentVersionController;
use App\Http\Controllers\Admin\FeeCandidateHistoryController;
use App\Http\Controllers\Admin\FeeFineController;
use App\Http\Controllers\Admin\FeeGroupController;
use App\Http\Controllers\Admin\FeeTypeController;
use App\Http\Controllers\Admin\FinanceAccountsController;
use App\Http\Controllers\Admin\FinanceVoucherController;
// Candidates
use App\Http\Controllers\Candidate\HomeController as Candidate;
use App\Http\Controllers\Candidate\PaymentController as CandidatePayment;
use App\Http\Controllers\Candidate\ProfileController as CandidateProfile;

// Sponsors
use App\Http\Controllers\Sponsor\HomeController as Sponsor;
use App\Http\Controllers\Sponsor\ProfileController as SponsorProfile;
use App\Http\Controllers\Sponsor\CandidateController as SponsorCandidate;


use App\Http\Controllers\RegistrationController;

use Illuminate\Support\Facades\Artisan;


//Invigilation
use App\Http\Controllers\Admin\InvigilationCandidateController;
use App\Http\Controllers\Admin\InvigilationListController;
use App\Http\Controllers\Admin\InvigilationPaymentMethodController;
use App\Http\Controllers\Admin\InvigilationReportController;
use App\Http\Controllers\Admin\InvigilationRoleController;
use App\Http\Controllers\Admin\InvigilationTypeController;
use App\Http\Controllers\Admin\InvigilatorTimeSheetController;
use App\Http\Controllers\Admin\InviglationProprietorController;

use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Admin\InvitationRecipientController;
use App\Http\Controllers\Admin\InvitationRoleController;
use App\Http\Controllers\Admin\InvitationScriptFeeController;

use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MenuPermissionController;
use App\Http\Controllers\Admin\PdfCategoryController;
use App\Http\Controllers\Admin\PdfController;
use App\Http\Controllers\Admin\PdfTemplateController;
use App\Http\Controllers\Admin\ServiceEmailController;
use App\Http\Controllers\Admin\SmsController;
use App\Http\Controllers\Admin\SmsTemplateController;
use App\Http\Controllers\Admin\SponsorController;
use App\Http\Controllers\School\InvigilatorController;



use App\Http\Controllers\School\InvitationController as SchoolInvitationController;




use App\Http\Controllers\Application\InvigilatorProfileController;
use App\Http\Controllers\Application\InvitationResponseController;
use App\Http\Controllers\Attendance\InvigilatorAttendanceController;
use App\Models\InvigilationCatergory;

use App\Http\Controllers\Admin\VisitorController;

use League\OAuth2\Client\Provider\Google;

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


//Sending Emails with OAuth2
Route::get('/auth/google', function () {

    $provider = new Google([
        'clientId' => config('mail.mailers.gmail.client_id'),
        'clientSecret' => config('mail.mailers.gmail.client_secret'),
        'redirectUri' => url('/auth/google/callback'), // Must match Google Cloud Console
    ]);

    if (!request()->has('code')) {
        $options = [
            'scope' => [
                'https://mail.google.com/',
                'https://www.googleapis.com/auth/gmail.send'
            ],
            'access_type' => 'offline',
            'prompt' => 'consent' // Important for getting refresh token
        ];

        return redirect($provider->getAuthorizationUrl($options));
    }

    try {
        $token = $provider->getAccessToken('authorization_code', [
            'code' => request('code')
        ]);

        // Store these securely!
        $accessToken = $token->getToken();
        $refreshToken = $token->getRefreshToken();
        $expires = $token->getExpires();

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires' => $expires
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
Route::get('/auth/google/callback', function () {
    return redirect('/auth/google?' . http_build_query(request()->all()));
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
Route::prefix('applications')->name('applications.')->group(function () {
    Route::get('{token}', [InvigilatorProfileController::class, 'index'])->name('index');
    Route::put('{token}', [InvigilatorProfileController::class, 'update'])->name('update');
    Route::put('declined/{token}', [InvigilatorProfileController::class, 'declined'])->name('declined');
    Route::get('exportSinglePdf/{id}', [InvigilatorProfileController::class, 'exportSinglePdf'])->name('exportSinglePdf');
    //Invitation
    Route::prefix('invitation')->name('invitation.')->group(function () {
        // Secure Response
        Route::get('/respond/{token}', [InvitationResponseController::class, 'showResponse'])->name('response');
        Route::post('/respond/{token}', [InvitationResponseController::class, 'submitResponse'])->name('submitResponse');
        Route::get('/respond/{token}/download', [InvitationResponseController::class, 'generatePdf'])->name('generatePdf');
    });
});

// Attendance
Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('invigilators', [InvigilatorAttendanceController::class, 'index'])->name('invigilators.index');
    Route::post('invigilators', [InvigilatorAttendanceController::class, 'store'])->name('invigilators.store');
});
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
        Route::get('/menus/guards', [MenuController::class, 'getGuards'])->name('menus.guards');

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
        // Menu Permission Routes
    Route::prefix('menu-permissions')->name('menu-permissions.')->group(function () {
    Route::get('/guards', [MenuPermissionController::class, 'getGuards'])->name('guards');    
    Route::get('/', [MenuPermissionController::class, 'index'])->name('index');
    Route::post('/', [MenuPermissionController::class, 'store'])->name('store');
    Route::delete('/{menuPermission}', [MenuPermissionController::class, 'destroy'])->name('destroy');
    Route::get('/menu/{menu}', [MenuPermissionController::class, 'getByMenu'])->name('menu');

});
Route::prefix('menus')->name('menus.')->group(function () {
    // Main Menu Routes
    Route::get('/', [MenuController::class, 'index'])->name('index');
    Route::get('/options', [MenuController::class, 'getMenuOptions'])->name('options');
    Route::get('/tree', [MenuController::class, 'getMenuTree'])->name('tree');
    Route::post('/reorder', [MenuController::class, 'reorder'])->name('reorder');
    Route::post('/', [MenuController::class, 'store'])->name('store');
    Route::get('/{menu}', [MenuController::class, 'edit'])->name('edit');
    Route::put('/{menu}', [MenuController::class, 'update'])->name('update');
    Route::delete('/{menu}', [MenuController::class, 'destroy'])->name('destroy');
   // Route::get('/guards', [MenuController::class, 'getGuards'])->name('guards');
   Route::get('/sidebar/refresh', [MenuController::class, 'refreshSidebar'])->name('sidebar.refresh');

});
// Route::prefix('visitors')->name('visitors.')->group(function () {
    
// });
// Route::get('/visitors/data', [VisitorController::class, 'data'])->name('visitors.data');
//     Route::resource('visitors', VisitorController::class);
//     Route::post('/visitors/store', [VisitorController::class, 'store'])->name('visitors.store');
//     Route::get('/visitors/{id}/edit', [VisitorController::class, 'edit'])->name('visitors.edit');
//     Route::put('/visitors/{id}', [VisitorController::class, 'update'])->name('visitors.update');
//     Route::delete('/visitors/{id}', [VisitorController::class, 'destroy'])->name('visitors.destroy');

Route::prefix('front-desk/phone-call-log')->name('front-desk.phone-call-log.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PhoneCallLogController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\PhoneCallLogController::class, 'store'])->name('store');
        Route::get('{phoneCallLog}/edit', [App\Http\Controllers\Admin\PhoneCallLogController::class, 'edit'])->name('edit');
        Route::put('{phoneCallLog}', [App\Http\Controllers\Admin\PhoneCallLogController::class, 'update'])->name('update');
        Route::delete('{phoneCallLog}', [App\Http\Controllers\Admin\PhoneCallLogController::class, 'destroy'])->name('destroy');
    });

Route::prefix('front-desk/postal-dispatch')->name('front-desk.postal-dispatch.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PostalDispatchController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\PostalDispatchController::class, 'store'])->name('store');
        Route::get('{postalDispatch}/edit', [App\Http\Controllers\Admin\PostalDispatchController::class, 'edit'])->name('edit');
        Route::put('{postalDispatch}', [App\Http\Controllers\Admin\PostalDispatchController::class, 'update'])->name('update');
        Route::delete('{postalDispatch}', [App\Http\Controllers\Admin\PostalDispatchController::class, 'destroy'])->name('destroy');
    });

Route::prefix('front-desk/visitors-book')->name('front-desk.visitors-book.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\VisitorBookController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\VisitorBookController::class, 'store'])->name('store');
        Route::get('{visitor}/edit', [App\Http\Controllers\Admin\VisitorBookController::class, 'edit'])->name('edit');
        Route::put('{visitor}', [App\Http\Controllers\Admin\VisitorBookController::class, 'update'])->name('update');
        Route::delete('{visitor}', [App\Http\Controllers\Admin\VisitorBookController::class, 'destroy'])->name('destroy');
    });

Route::prefix('front-desk')->name('front-desk.')->group(function () {
        Route::resource('postal-receive', App\Http\Controllers\Admin\PostalReceiveController::class)->parameters([
            'postal-receive' => 'postalReceive'
        ])->except(['create']);
        
        // Override edit and show routes to return JSON
        Route::get('postal-receive/{postalReceive}/edit', [App\Http\Controllers\Admin\PostalReceiveController::class, 'edit'])->name('postal-receive.edit');
        Route::get('postal-receive/{postalReceive}', [App\Http\Controllers\Admin\PostalReceiveController::class, 'show'])->name('postal-receive.show');

        Route::resource('enquiry', App\Http\Controllers\Admin\EnquiryController::class)->parameters([
            'enquiry' => 'enquiry'
        ])->except(['create']);
        
        // Override edit and show routes to return JSON
        Route::get('enquiry/{enquiry}/edit', [App\Http\Controllers\Admin\EnquiryController::class, 'edit'])->name('enquiry.edit');
        Route::get('enquiry/{enquiry}', [App\Http\Controllers\Admin\EnquiryController::class, 'show'])->name('enquiry.show');

    });

    
    
    
    






        
        //'middleware' => 'admin'

        Route::get('/sms', [SmsController::class, 'index'])->name('sms.index');
        Route::get('/sms/contacts', [SmsController::class, 'getContacts'])->name('sms.getContacts');
        Route::post('/sms/send', [SmsController::class, 'send'])->name('sms.send');
        Route::get('/sms/templates', [SmsTemplateController::class, 'getTemplates'])->name('sms.getTemplates');
        Route::post('/sms/templates', [SmsTemplateController::class, 'saveTemplate'])->name('sms.saveTemplate');

        Route::get('/', [AdminHome::class, 'index'])->name('home');
        Route::get('/registeredSubjects', [AdminHome::class, 'registeredSubjects'])->name('registeredsubjects');
        // ************Centers**********************
        Route::get('/centers/all', [CentersController::class, 'allCenters'])->name('centers.allCenters');
        Route::get('/centers/update-status', [CentersController::class, 'updateStatus'])->name('centers.updateStatus');
        Route::put('/centers/update-sessions/{id}', [CentersController::class, 'updateSessions'])->name('centers.updateSessions');
        Route::put('/centers/update-level/{id}', [CentersController::class, 'updateLevels'])->name('centers.updateLevels');
        Route::put('/centers/update-subjects/{id}', [CentersController::class, 'updateSubjects'])->name('centers.updateSubjects');
        
        // invitations
        Route::prefix('invitations')->name('invitations.')->group(function () {
            // Invitations
            Route::get('/', [InvitationController::class, 'index'])->name('index');
            Route::post('/', [InvitationController::class, 'store'])->name('store');
            Route::get('/{invitation}/edit', [InvitationController::class, 'edit'])->name('edit');
            Route::put('/{invitation}', [InvitationController::class, 'update'])->name('update');
            Route::delete('/{invitation}/delete', [InvitationController::class, 'destroy'])->name('destroy');
            Route::post('/{invitation}/resend', [InvitationController::class, 'resend'])->name('resend');
            Route::post('/bulk/resend/invitations', [InvitationController::class, 'bulkResend'])->name('bulk-resend');
            Route::get('/csv/template', [InvitationController::class, 'downloadTemplate'])->name('downloadTemplate');
            Route::post('/import-csv', [InvitationController::class, 'importCsv'])->name('importCsv');
            Route::get('/export-csv/all/invitation', [InvitationController::class, 'exportCsv'])->name('exportCsv');
            Route::resource('script-fee', InvitationScriptFeeController::class);

            // Add workflow routes
            Route::post('/{invitation}/start-workflow', [InvitationController::class, 'startWorkflow'])->name('start-workflow');
            Route::get('/{invitation}/workflow', [InvitationController::class, 'workflowStatus'])->name('workflow.status');

            //Recipients
            Route::prefix('recipients')->name('recipients.')->group(function () {
                Route::get('/{recipient}/edit', [InvitationRecipientController::class, 'edit'])->name('edit');
                Route::put('/{recipient}', [InvitationRecipientController::class, 'update'])->name('update');
                Route::delete('/{recipient}/delete', [InvitationRecipientController::class, 'destroy'])->name('destroy');
            });
            //Roles
            Route::prefix('roles')->name('roles.')->group(function () {
                Route::get('/', [InvitationRoleController::class, 'index'])->name('index');
                Route::post('/', [InvitationRoleController::class, 'store'])->name('store');
                Route::get('/{role}/fields', [InvitationRoleController::class, 'getFields'])->name('fields');
                Route::get('/{role}/edit', [InvitationRoleController::class, 'edit'])->name('edit');
                Route::post('/copy/{role}/positions', [InvitationRoleController::class, 'copyPositions'])->name('copyPositions');
                Route::get('/{role}/designer', [InvitationRoleController::class, 'designer'])->name('designer');
                Route::get('/{role}/pdf-template', [InvitationRoleController::class, 'pdfTemplate'])->name('pdfTemplate');
                Route::post('/editor', [InvitationRoleController::class, 'saveField'])->name('saveField');
                Route::post('/editor/remove-field', [InvitationRoleController::class, 'removeField'])->name('removeField');
                Route::post('/editor/copy-field', [InvitationRoleController::class, 'copyField'])->name('copyField');
                Route::put('/{role}', [InvitationRoleController::class, 'update'])->name('update');
                Route::delete('/{role}', [InvitationRoleController::class, 'destroy'])->name('destroy');
            });
        });

        Route::prefix('workflows')->name('workflows.')->group(function () {
            // Workflow Definitions
            Route::get('/', [WorkflowController::class, 'index'])->name('index');
            Route::get('/create', [WorkflowController::class, 'create'])->name('create');
            Route::post('/', [WorkflowController::class, 'store'])->name('store');
            Route::get('/{workflow}/edit', [WorkflowController::class, 'edit'])->name('edit');
            Route::put('/{workflow}', [WorkflowController::class, 'update'])->name('update');
            Route::delete('/{workflow}', [WorkflowController::class, 'destroy'])->name('destroy');
            Route::get('/{workflow}/steps', [WorkflowController::class, 'steps'])->name('steps');

            // Workflow Instances
            Route::post('/start', [WorkflowInstanceController::class, 'start'])->name('instances.start');
            Route::get('/instances/{instance}', [WorkflowInstanceController::class, 'show'])->name('instances.show');

            // Approvals (stay within the current admin/workflows group)
            // Route::prefix('approvals')->name('approvals.')->group(function () {
            //     Route::get('/', [ApprovalController::class, 'index'])->name('index');
            //     Route::post('/{instance}/process', [ApprovalController::class, 'process'])->name('process');
            //     Route::get('/{instance}/history', [ApprovalController::class, 'history'])->name('history');
            // });
        });






        // invigilation
        Route::prefix('invigilations')->name('invigilations.')->group(function () {
            // routes here
            Route::resource('categories', InvigilationCatergory::class);
            Route::resource('proprietors', InviglationProprietorController::class);
            Route::resource('types', InvigilationTypeController::class);
            Route::resource('candidatesrange', InvigilationCandidateController::class);
            Route::resource('roles', InvigilationRoleController::class);
            Route::resource('paymentmethods', InvigilationPaymentMethodController::class);
            Route::get('/exportSinglePdf/{id}', [InvigilationListController::class, 'exportSinglePdf'])->name('contracts.exportSinglePdf');
            Route::get('/exportMultiPdf', [InvigilationReportController::class, 'exportMultiPdf'])->name('contracts.exportMultiPdf');
            Route::get('/invigilationReport', [InvigilationListController::class, 'invigilationReport'])->name('invigilationReport');
            Route::resource('contracts', InvigilationListController::class);
            Route::get('download', [InvigilatorTimeSheetController::class, 'downloadCSV'])->name('timesheet.download');
            Route::post('import', [InvigilatorTimeSheetController::class, 'import'])->name('import');
            Route::get('/get-timesheet', [InvigilatorTimesheetController::class, 'getSubjects'])->name('timesheet.getsubjects');
            Route::resource('timesheet', InvigilatorTimeSheetController::class);
        });

        // ************Documents categories**********************

        Route::resource('document-categories', DocumentCategoryController::class);
        // ************Documents**********************
        Route::get('/assigned/documents', [DocumentController::class, 'assignedDocuments'])->name('documents.assigned');
        Route::get('/all/users-roles', [DocumentController::class, 'getRoleUser'])->name('documents.getRoleUser');
        Route::get('/download/id/{id}/isVersion/{isVersion}', [DocumentController::class, 'downloadDocument'])->name('documents.download');
        Route::resource('documents', DocumentController::class);
        Route::resource('documents-comments', DocumentCommentController::class);
        Route::resource('documents-versions', DocumentVersionController::class);

        Route::prefix('documents')->name('documents.')->group(function () {
            //Permission
            Route::prefix('permissions')->name('permissions.')->group(function () {
                Route::get('/{id}', [DocumentPermissionController::class, 'index'])->name('index');
                Route::post('/{id}/users', [DocumentPermissionController::class, 'documentUserPermission'])->name('users.store');
                Route::delete('/{id}/users/permissions', [DocumentPermissionController::class, 'deleteDocumentUserPermission'])->name('users.destroy');
                Route::post('/{id}/roles', [DocumentPermissionController::class, 'documentRolePermission'])->name('roles.store');
                Route::delete('/{id}/roles/permissions', [DocumentPermissionController::class, 'deleteDocumentRolePermission'])->name('roles.destroy');
            });
            Route::prefix('multipermissions')->name('multipermissions.')->group(function () {
                Route::get('/multi-permissions', [DocumentPermissionControllerMulti::class, 'index'])->name('index');
                Route::get('/centers/all', [DocumentPermissionControllerMulti::class, 'centersAccounts'])->name('centersAccounts');
                Route::post('/multi/permissions', [DocumentPermissionControllerMulti::class, 'multipleDocumentsToUser'])->name('multipleDocumentsToUser');
            });
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

        Route::resource('sponsor-users', SponsorUserController::class);

        // ************Roles & Permissions**********************
        Route::resource('permissions', PermissionController::class);
        Route::put('/role/{id}', [RoleController::class, 'updateRolePermission'])->name('roles.updateRolePermission');
        Route::resource('roles', RoleController::class);
        // ************Subjects*********************************

        Route::get('/subjects/sync/timetable', [SubjectController::class, 'syncToTimetable'])->name('subjects.syncToTimetable');
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
        Route::get('/candidates-entries/autocompleteSearchCenter', [CandidateEntryController::class, 'autocompleteSearchCenter'])->name('candidates.entries.autocompleteSearchCenter');
        Route::get('/candidates-entries/autocompleteSearchSubject', [CandidateEntryController::class, 'autocompleteSearchSubject'])->name('candidates.entries.autocompleteSearchSubject');

        Route::get('/candidates-entries/autocompleteSearchCenter', [CandidateEntryController::class, 'autocompleteSearchCenter'])->name('candidates.entries.autocompleteSearchCenter');





        // ************TimeTables*********************************
        Route::get('/timetable', [TimeTableController::class, 'index'])->name('timetable.index');
        Route::post('/timetable-update', [TimeTableController::class, 'update'])->name('timetable.update');
        // certificate
        Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
        Route::post('/certificates', [CertificateController::class, 'print'])->name('certificates.print');



        // ****************PDF **********************

        Route::prefix('pdf')->name('pdf.')->group(function () {
            Route::resource('templates', PdfTemplateController::class);
            Route::resource('categories', PdfCategoryController::class);
            Route::get('/designer/{template_id}', [PdfController::class, 'index'])->name('designer.index');
            Route::post('/designer/store', [PdfController::class, 'storeElement'])->name('designer.store-element');
            Route::get('/designer/edit/{id}', [PdfController::class, 'findElement'])->name('designer.edit-element');
            Route::put('/designer/{id}', [PdfController::class, 'updateElement'])->name('designer.update-element');
            Route::post('/designer/save-element', [PdfController::class, 'saveElementPositions'])->name('designer.save-element-positions');
            Route::get('/designer/get-table-columns/{table}', [PdfController::class, 'getTableColumns'])->name('designer.table-columns');
            // ****************Over Print**********************
            Route::get('/over-print', [OverPrintController::class, 'index'])->name('over-print.index');
            Route::post('/over-print', [OverPrintController::class, 'print'])->name('over-print.print');
            Route::post('/over-print-pdf', [OverPrintController::class, 'overPrint'])->name('over-print.pdf');
        });





        // ****************Fees Charges**********************
        Route::prefix('fees-stracture')->name('fees-stracture.')->group(function () {
            Route::resource('types', FeeTypeController::class);
            Route::get('/detail', [FeeGroupController::class, 'feeDetail'])->name('groups.detail');
            Route::delete('/detail/{id}', [FeeGroupController::class, 'destroyDetail'])->name('groups.destroyDetail');
            Route::put('/detail/{id}', [FeeGroupController::class, 'updateDetail'])->name('groups.updateDetail');
            Route::post('/multdetails/{id}', [FeeGroupController::class, 'updateMultDetails'])->name('groups.updateMultDetails');
            Route::resource('groups', FeeGroupController::class);
            Route::resource('fines', FeeFineController::class);
            Route::resource('fee-histories', FeeCandidateHistoryController::class);
        });

        // ****************Accounting**********************
        Route::prefix('accounting')->name('accounting.')->group(function () {
            Route::resource('accounts', FinanceAccountsController::class);
            Route::resource('vouchers', FinanceVoucherController::class);
        });

        Route::resource('fees', FeesController::class);
        Route::resource('late-fees', LateFeeController::class);

        // ****************Payment History *******************
        Route::get('/payment-history-invoice', [PaymentHistoryController::class, 'index'])->name('payment-history.index');
        Route::get('/payment-history-payment', [PaymentHistoryController::class, 'paymentHistory'])->name('payment-history.payments');
        // ****************Payment Verification *******************
        // centers
        Route::prefix('centre-collection')->name('centre-collection.')->group(function () {
            Route::get('center/{center_no}', [CentreCollectionController::class, 'center_collection'])->name('fees.center');
            Route::resource('fees', CentreCollectionController::class);
            // ******************Center charges *******************
            Route::resource('center-charges', CenterChargesController::class);
        });
        //



        Route::get('/Collection/{id}', [SponsorController::class, 'editSponsorCollection'])->name('sponsors.editSponsorCollection');
        Route::put('/Collection/{id}', [SponsorController::class, 'updateSponsorCollection'])->name('sponsors.updateSponsorCollection');
        Route::get('/All/Collection', [SponsorController::class, 'getSponsorAllCollection'])->name('sponsors.getSponsorAllCollection');
        Route::get('/Collection', [SponsorController::class, 'getSponsorCollection'])->name('sponsors.getSponsorCollection');
        Route::post('/Collection', [SponsorController::class, 'storeSponsorCollection'])->name('sponsors.storeSponsorCollection');
        // ******************funders *******************
        Route::resource('sponsors', SponsorController::class);



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
        Route::resource('service-emails', ServiceEmailController::class);
        Route::resource('service-item', ServicesItemContoller::class);

        Route::resource('service-requirements', ServiceRequirementContoller::class);

        Route::get('/service-sales/export-files', [ServiceSaleContolller::class, 'exportFiles'])->name('service-sales.exportFiles');
        Route::get('/service-sales/comments/{id}', [ServiceSaleContolller::class, 'editComments'])->name('service-sales.editcomments');
        Route::put('/service-sales/comments/{id}', [ServiceSaleContolller::class, 'updateComments'])->name('service-sales.updatecomments');
        Route::resource('service-sales', ServiceSaleContolller::class);




        // ******************Logs*********************
        Route::get('/user-activity', [LogController::class, 'index'])->name('logs.index');
        Route::post('/logs', [LogController::class, 'setActitiesLogs'])->name('logs.setActitiesLogs');


        // ******************Setting *********************
        Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::put('/setting/{id}', [SettingController::class, 'update'])->name('setting.update');

        // ******************Page menus*********************
        Route::post('page-menus/updateOrder', [MenuController::class, 'updateOrder'])->name('page-menus.updateOrder');
        Route::resource('page-menus', MenuController::class);
        


        



        // *************Publications Publication ************
        Route::get('/publications', [PublicationController::class, 'index'])->name('publications.index');
        Route::get('/publications-display', [PublicationController::class, 'displayPublications'])->name('publications.display');
        Route::put('/publications/{id}', [PublicationController::class, 'update'])->name('publications.update');
        Route::post('/publications', [PublicationController::class, 'store'])->name('publications.store');

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

        Route::get('/download/id/{id}/isVersion/{isVersion}', [SchoolDocumentController::class, 'downloadDocument'])->name('documents.download');
        Route::resource('documents', SchoolDocumentController::class);
        Route::resource('invigilators', InvigilatorController::class);

        Route::get('/invitations/{type}', [SchoolInvitationController::class, 'index'])->name('invitations.index');
        Route::post('/invitations/action', [SchoolInvitationController::class, 'process'])->name('invitations.process');


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


        Route::get('/receipt/{id}', [CandidatePayment::class, 'printReceipt'])->name('payment.receipt');

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
        Route::get('/', [SponsorCandidate::class, 'index'])->name('home');
        Route::get('/candidate', [SponsorCandidate::class, 'index'])->name('candidate.index');
        Route::post('/center-level', [SponsorCandidate::class, 'centers'])->name('candidate.centers');
        Route::get('/profile', [SponsorProfile::class, 'index'])->name('profile.index');
        //Approve
        Route::get('/candidate/{id}/edit', [SponsorCandidate::class, 'edit'])->name('candidate.edit');
        Route::put('/candidate/{id}', [SponsorCandidate::class, 'update'])->name('candidate.update');
    });
});
