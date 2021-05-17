<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\HomeController as AdminHome;
use App\Http\Controllers\Admin\StoreController as AdminStore;
use App\Http\Controllers\Admin\BusinessTypeController as AdminBusinessType;



use App\Http\Controllers\StoreController as Store;
use App\Http\Controllers\ServiceCategoryController as ServiceCategory;
use App\Http\Controllers\AdditionaltaxController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BillingController;



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


// User Routes
Route::group(['middleware' => ['auth', 'store']], function () {

    // Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Store Routes
    $store_link = 'store';
    Route::get($store_link . '/profile', [Store::class, 'index']);
    Route::post($store_link . '/unique', [Store::class, 'isUnique']);
    Route::put($store_link . '/update/{id}', [Store::class, 'update']);
    Route::put($store_link . '/update/billing/{id}', [Store::class, 'storeBilling']);

    // User Routes
    Route::resource('users', UserController::class)->except(['show']);
    Route::get('users/lists', [UserController::class, 'lists']);
    Route::post( 'users/unique', [UserController::class, 'isUnique']);

    // Business type Routes
    $business_type = 'business-types';
    Route::resource($business_type, AdminBusinessType::class)->except(['show']);
    Route::get($business_type . '/lists', [AdminBusinessType::class, 'lists']);

    Route::get('change-password', [ProfileController::class, 'index']);
    Route::post('change-password', [ProfileController::class, 'update']);

    // Roles Routes
    Route::resource('roles', RoleController::class);

    // Serice category Routes
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

    // Customer Routes
    $customer = 'customers';
    Route::resource($customer, CustomerController::class)->except(['show']);
    Route::get($customer . '/lists', [CustomerController::class, 'lists']);
    Route::get($customer . 'autocomplete', [CustomerController::class, 'autocomplete'])->name('billing.autocomplete');

    // Additionaltax Routes
    $additionaltax = 'additional-tax';
    Route::resource($additionaltax, AdditionaltaxController::class)->except(['show']);
    Route::get($additionaltax . '/lists', [AdditionaltaxController::class, 'lists']);

    // Billing Routes
    $billing = 'billings';
    Route::resource($billing, BillingController::class)->except(['show']);;
    Route::get($billing . '/lists', [BillingController::class, 'lists']);

    $link = 'common';
    Route::get($link . '/get-states', [CommonController::class, 'getStates']);    
    Route::get($link . '/get-districts', [CommonController::class, 'getDistricts']);    
    Route::get($link . '/get-all-services', [CommonController::class, 'getAllServices']);    
    Route::get($link . '/get-all-packages', [CommonController::class, 'getAllPackages']);    
    Route::get($link . '/get-districts', [CommonController::class, 'getDistricts']);    
    Route::post($link . '/get-shop-districts', [CommonController::class, 'getShopDistricts']);    
    Route::post($link . '/get-customer-details', [CommonController::class, 'getCustomerDetails']);   
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
