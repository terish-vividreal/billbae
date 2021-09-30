<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\HomeController as AdminHome;
use App\Http\Controllers\Admin\StoreController as AdminStore;
use App\Http\Controllers\Admin\BusinessTypeController as AdminBusinessType;
use App\Http\Controllers\Auth\ForgotPasswordController;


use App\Http\Controllers\StoreController as Store;
use App\Http\Controllers\ServiceCategoryController as ServiceCategory;
use App\Http\Controllers\AdditionaltaxController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CashbookController;
use App\Http\Controllers\PaymentTypeController;





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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post'); 
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::get('create-password/{token}', [ForgotPasswordController::class, 'showCreatePasswordForm'])->name('create.password.get');
Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');
Route::post('create-password', [ForgotPasswordController::class, 'submitCreatePasswordForm'])->name('create.password.post');

// Forgot password routes
// Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
// Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post'); 
// Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
// Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');


// User Routes
Route::group(['middleware' => ['auth', 'store']], function () {

    // Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');


    //Schedule Routes
    $schedule = 'schedules';
    Route::resource($schedule, ScheduleController::class);
    Route::get($schedule.'/lists', [ScheduleController::class, 'lists']);
    Route::post($schedule.'/save-booking', [ScheduleController::class, 'storeSchedule']);
    Route::post($schedule.'/re-schedule', [ScheduleController::class, 'reSchedule']);
    // Route::post($schedule.'/update/{id}', [ScheduleController::class, 'updateSchedule']);

    // Store Routes
    $store_link = 'store';
    Route::get($store_link . '/profile', [Store::class, 'index']);
    Route::post($store_link . '/unique', [Store::class, 'isUnique']);
    Route::post($store_link . '/update-logo', [Store::class, 'updateLogo']);
    // Route::post($store_link . '/crop-image-upload', [Store::class, 'updateLogo']);
    Route::get($store_link . '/billings', [Store::class, 'billings']);
    Route::post($store_link . '/update/gst-billing', [Store::class, 'updateGst']);
    Route::get($store_link . '/billing-series', [Store::class, 'billingSeries']);
    Route::put($store_link . '/update/{id}', [Store::class, 'update']);
    Route::put($store_link . '/update/billing/{id}', [Store::class, 'storeBilling']);
    Route::post($store_link . '/update/bill-format/', [Store::class, 'updateBillFormat']);
    Route::post($store_link . '/theme-settings', [Store::class, 'themeSettings']);
   
    // User profile routes
    Route::get($store_link . '/user-profile', [Store::class, 'userProfile']);
    Route::post($store_link . '/user-profile', [Store::class, 'postUserProfile']);
    Route::post($store_link . '/update-user-image', [Store::class, 'updateUserImage']);

    
    // User Routes
    Route::resource('users', UserController::class)->except(['show']);
    Route::get('users/lists', [UserController::class, 'lists']);
    Route::post( 'users/unique', [UserController::class, 'isUnique']);
    Route::post( 'users/manage-status', [UserController::class, 'manageStatus']);
    Route::post('users/update-password', [UserController::class, 'updatePassword']);

    //Staff Routes
    $staff = 'staffs';
    Route::resource($staff, StaffController::class)->except(['show']);
    Route::get($staff.'/lists', [StaffController::class, 'lists']);
    Route::get($staff.'/{id}/manage-document', [StaffController::class, 'manageDocument']);


    Route::post($staff.'/update/user-image', [StaffController::class, 'updateUserImage']);
    Route::post($staff.'/get-document', [StaffController::class, 'getDocument']);
    Route::post($staff.'/upload-id-proof', [StaffController::class, 'uploadIdProofs']);
    Route::post($staff.'/remove-id-proof', [StaffController::class, 'removeIdProofs']);
    Route::post($staff.'/delete-id-proof', [StaffController::class, 'deleteIdProofs']);
    Route::get($staff.'/download-files/{document}', [StaffController::class, 'downloadFile'])->name('download-files');
    Route::post($staff.'/update/document-details', [StaffController::class, 'updateDocumentDetails']);
    Route::post($staff.'/store-document', [StaffController::class, 'storeDocuments']);
    Route::post($staff.'/remove-temp-document', [StaffController::class, 'removeTempDocuments']);

    // Business type Routes
    // $business_type = 'business-types';
    // Route::resource($business_type, AdminBusinessType::class)->except(['show']);
    // Route::get($business_type . '/lists', [AdminBusinessType::class, 'lists']);

    Route::get('change-password', [ProfileController::class, 'index']);
    Route::post('change-password', [ProfileController::class, 'update']);

    // Roles Routes
    // Route::resource('roles', RoleController::class);

    // Service category Routes
    $service_category = 'service-category';
    Route::resource($service_category, ServiceCategory::class)->except(['show']);
    Route::get($service_category . '/lists', [ServiceCategory::class, 'lists']);

    // Country Routes
    $country = 'country';
    Route::resource($country, CountryController::class)->except(['show']);
    Route::get($country . '/lists', [CountryController::class, 'lists']);

    // State Routes
    $state = 'states';
    Route::resource($state, StateController::class)->except(['show']);
    Route::get($state . '/lists', [StateController::class, 'lists']);

    // District Routes
    $district = 'districts';
    Route::resource($district, DistrictController::class)->except(['show']);
    Route::get($district . '/lists', [DistrictController::class, 'lists']);

    // Services Routes
    $services = 'services';
    Route::resource($services, ServiceController::class)->except(['show']);
    Route::get($services . '/lists', [ServiceController::class, 'lists']);
    Route::get($services . '/select-list', [ServiceController::class, 'lists']);

    // Packages Routes
    $packages = 'packages';
    Route::resource($packages, PackageController::class)->except(['show']);
    Route::get($packages . '/lists', [PackageController::class, 'lists']);
    Route::post($packages . '/update-status', [PackageController::class, 'updateStatus']);

    // Customer Routes
    $customer = 'customers';
    Route::resource($customer, CustomerController::class)->except(['show']);
    Route::get($customer . '/lists', [CustomerController::class, 'lists']);
    Route::get($customer . '/autocomplete', [CustomerController::class, 'autocomplete'])->name('billing.autocomplete');

    // Additionaltax Routes
    $additionaltax = 'additional-tax';
    Route::resource($additionaltax, AdditionaltaxController::class)->except(['show']);
    Route::get($additionaltax . '/lists', [AdditionaltaxController::class, 'lists']);

    // Payment Type Routes
    $paymentTypes = 'payment-types';
    Route::resource($paymentTypes, PaymentTypeController::class)->except(['show']);
    Route::get($paymentTypes . '/lists', [PaymentTypeController::class, 'lists']);
    Route::get($paymentTypes . '/select-list', [PaymentTypeController::class, 'lists']);

    // Billing Routes 
    $billing = 'billings';
    Route::resource($billing, BillingController::class)->except(['show']);
    Route::get($billing . '/lists', [BillingController::class, 'lists']);
    Route::get($billing . '/show/{id}', [BillingController::class, 'show']);
    Route::post($billing . '/manage-discount', [BillingController::class, 'manageDiscount']);
    Route::post($billing . '/get-invoice-data', [BillingController::class, 'getInvoiceData']);
    Route::get($billing . '/invoice/{id}', [BillingController::class, 'invoice']);
    Route::put($billing . '/invoice/update/{id}', [BillingController::class, 'updateInvoice']);
    Route::get($billing . '/invoice/edit/{id}', [BillingController::class, 'editInvoice']);
    Route::get($billing .'/invoice-data/generate-pdf/{id}', [BillingController::class, 'generatePDF']);
    Route::post($billing . '/add-new-customer', [BillingController::class, 'storeCustomer']);
    Route::post($billing . '/store-payment', [BillingController::class, 'storePayment']);
    Route::post($billing . '/cancel/{billing}', [BillingController::class, 'cancelBill']);

    $link = 'common';
    Route::get($link . '/get-states', [CommonController::class, 'getStates']);    
    Route::get($link . '/get-districts', [CommonController::class, 'getDistricts']);    
    Route::get($link . '/get-all-services', [CommonController::class, 'getAllServices']);    
    Route::post($link . '/get-services', [CommonController::class, 'getServices']);    
    Route::post($link . '/get-packages', [CommonController::class, 'getPackages']);    
    Route::get($link . '/get-all-packages', [CommonController::class, 'getAllPackages']);    
    Route::get($link . '/get-districts', [CommonController::class, 'getDistricts']);    
    Route::post($link . '/get-shop-districts', [CommonController::class, 'getShopDistricts']);    
    Route::post($link . '/get-shop-states', [CommonController::class, 'getShopStates']);    
    Route::post($link . '/get-customer-details', [CommonController::class, 'getCustomerDetails']);   
    Route::post($link . '/get-taxdetails', [CommonController::class, 'calculateTax']);  
    Route::post($link . '/list-service-with-tax', [CommonController::class, 'calculateTaxTable']);  
    Route::post($link . '/get-timezone', [CommonController::class, 'getTimezone']);  
    Route::post($link . '/get-states-of-country', [CommonController::class, 'getStatesOfCountry']);    
    Route::post($link . '/get-districts-of-state', [CommonController::class, 'getDistrictsOfState']);    
    Route::post($link . '/get-currencies', [CommonController::class, 'getCurrencies']);    
    Route::post($link . '/get-all-therapists', [CommonController::class, 'getAllTherapists']);    
    Route::post($link . '/get-therapist/{id}', [CommonController::class, 'getTherapist']);    
    Route::post($link . '/billings/get-report-by-date/', [CommonController::class, 'getBillingReports']);    
    

    
    
    // Report Routes 
    $reports = 'reports';
    Route::get($reports . '/sales-report', [ReportController::class, 'salesReport']);
    Route::post($reports . '/get-sales-chart-data', [ReportController::class, 'getSalesReportChartData']);
    Route::get($reports . '/get-sales-table-data', [ReportController::class, 'getSalesReportTableData']);

    // Cashbook Routes 
    $cashbook = 'cashbook';
    Route::resource($cashbook, CashbookController::class)->except(['show']);
    Route::get($cashbook . '/lists', [CashbookController::class, 'lists']);
    Route::post($cashbook . '/withdraw', [CashbookController::class, 'withdraw']);

    Route::get('send-mail', function () {
   
        $details = [
            'title' => 'Mail from Billbae',
            'body' => 'This is for testing email using smtp'
        ];
       
        \Mail::to('ajesh.ks@vividreal.com')->send(new \App\Mail\MyTestMail($details));
       
        dd("Email is Sent...");
    });
    
});

// Super Admin Routes
Route::prefix('admin/')->group(function () {
    Route::group(['middleware' => ['auth', 'admin']], function () {

        // Dashboard
        Route::get('home', [AdminHome::class, 'index'])->name('admin.home');
        
        // Store Routes
        $store_link = 'stores';
        Route::resource($store_link, AdminStore::class)->except(['show']);
        Route::get($store_link . '/lists', [AdminStore::class, 'lists']);

        // Business type Routes
        $business_type = 'business-types';
        Route::resource($business_type, AdminBusinessType::class)->except(['show']);
        Route::get($business_type . '/lists', [AdminBusinessType::class, 'lists']);

        // Roles Routes
        Route::resource('roles', RoleController::class);
    });

});
