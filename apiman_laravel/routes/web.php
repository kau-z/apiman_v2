<?php

use App\Http\Controllers\ApiConfigurationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DbConfigurationController;
use App\Http\Controllers\SyncApiController;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::get('/', [AuthController::class, 'showLogin'])->name('home');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout.post');

// CodeIgniter legacy URL compatibility redirects / aliases
Route::get('/welcome', [AuthController::class, 'showLogin']);
Route::get('/welcome/login', [AuthController::class, 'showLogin']);
Route::post('/welcome/login_validation', [AuthController::class, 'login']);
Route::get('/welcome/logout', [AuthController::class, 'logout']);
Route::get('/welcome/register', [AuthController::class, 'showRegister']);
Route::get('/welcome/enter', [SyncApiController::class, 'create']);
Route::get('/welcome/view', [SyncApiController::class, 'index']);
Route::get('/welcome/edit', [SyncApiController::class, 'edit']);
Route::match(['get', 'post'], '/welcome/dashboard', [DashboardController::class, 'index']);

// Protected Web Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::match(['get', 'post'], '/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Sync API Management
    Route::get('/sync-api', [SyncApiController::class, 'index'])->name('sync-api.index');
    Route::get('/sync-api/create', [SyncApiController::class, 'create'])->name('sync-api.create');
    Route::post('/sync-api', [SyncApiController::class, 'store'])->name('sync-api.store');
    Route::get('/sync-api/{id}/edit', [SyncApiController::class, 'edit'])->name('sync-api.edit');
    Route::post('/sync-api/update', [SyncApiController::class, 'update'])->name('sync-api.update');
    Route::post('/sync-api/category', [SyncApiController::class, 'category'])->name('sync-api.category');
    Route::get('/sync-api/details/{id}', [SyncApiController::class, 'getDetails'])->name('sync-api.details');

    // Legacy Sync_API endpoints for frontend forms
    Route::post('/Sync_API/api_validation', [SyncApiController::class, 'store']);
    Route::post('/Sync_API/update', [SyncApiController::class, 'update']);
    Route::post('/Sync_API/category', [SyncApiController::class, 'category']);

    // API Configurations Resource
    Route::resource('api-configurations', ApiConfigurationController::class)->names('api-configurations');
    // Legacy CodeIgniter URLs for ApiConfigurations
    Route::get('/ApiConfigurations/create', [ApiConfigurationController::class, 'create']);
    Route::post('/ApiConfigurations/store', [ApiConfigurationController::class, 'store']);
    Route::get('/ApiConfigurations/edit/{id}', [ApiConfigurationController::class, 'edit']);
    Route::post('/ApiConfigurations/update', [ApiConfigurationController::class, 'update']);
    Route::get('/ApiConfigurations/delete/{id}', [ApiConfigurationController::class, 'destroy']);

    // DB Configurations Resource
    Route::resource('db-configurations', DbConfigurationController::class)->names('db-configurations');
    // Legacy CodeIgniter URLs for DbConfigurations
    Route::get('/DbConfigurations/create', [DbConfigurationController::class, 'create']);
    Route::post('/DbConfigurations/store', [DbConfigurationController::class, 'store']);
    Route::get('/DbConfigurations/edit/{id}', [DbConfigurationController::class, 'edit']);
    Route::post('/DbConfigurations/update', [DbConfigurationController::class, 'update']);
    Route::get('/DbConfigurations/delete/{id}', [DbConfigurationController::class, 'destroy']);

    // Company Registration
    Route::get('/company/create', [CompanyController::class, 'create'])->name('company.create');
    Route::post('/company', [CompanyController::class, 'store'])->name('company.store');
    Route::post('/Company/create', [CompanyController::class, 'store']);
});

// Legacy Datalayer routes without /api prefix
Route::middleware([\App\Http\Middleware\ApiBasicAuth::class])->group(function () {
    Route::post('/datalayer/general', [\App\Http\Controllers\Api\DatalayerController::class, 'general']);
    Route::post('/datalayer/general_api_calling', [\App\Http\Controllers\Api\DatalayerController::class, 'general']);
    Route::post('/Datalayer/general', [\App\Http\Controllers\Api\DatalayerController::class, 'general']);
    Route::post('/Datalayer/general_api_calling', [\App\Http\Controllers\Api\DatalayerController::class, 'general']);
    Route::post('/Datalayer/magiclight', [\App\Http\Controllers\Api\DatalayerController::class, 'magiclight']);
    Route::post('/datalayer/magiclight', [\App\Http\Controllers\Api\DatalayerController::class, 'magiclight']);
});
