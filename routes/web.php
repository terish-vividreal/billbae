<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\HomeController as AdminHome;
use App\Http\Controllers\Admin\StoreController as AdminStore;
use App\Http\Controllers\Admin\BusinessTypeController as AdminBusinessType;



use App\Http\Controllers\StoreController as Store;
use App\Http\Controllers\ServiceCategoryController as ServiceCategory;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\StateController;


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
    Route::resource($service_category, ServiceCategory::class)->except(['show']);;
    Route::get($service_category . '/lists', [ServiceCategory::class, 'lists']);

    // Country Routes
    $country = 'country';
    Route::resource($country, CountryController::class)->except(['show']);;
    Route::get($country . '/lists', [CountryController::class, 'lists']);

    // State Routes
    $state = 'states';
    Route::resource($state, StateController::class)->except(['show']);;
    Route::get($state . '/lists', [StateController::class, 'lists']);
    

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
