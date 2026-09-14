<?php

use App\Http\Controllers\Api\DatalayerController;
use App\Http\Middleware\ApiBasicAuth;
use Illuminate\Support\Facades\Route;

Route::middleware([ApiBasicAuth::class])->group(function () {
    // Magiclight test
    Route::post('/magiclight', [DatalayerController::class, 'magiclight']);
    Route::post('/datalayer/magiclight', [DatalayerController::class, 'magiclight']);

    // General sync
    Route::post('/general', [DatalayerController::class, 'general']);
    Route::post('/datalayer/general', [DatalayerController::class, 'general']);
    Route::post('/datalayer/general_api_calling', [DatalayerController::class, 'general']);
    Route::post('/general_api_calling', [DatalayerController::class, 'general']);

    // Subcontractor
    Route::post('/subcontractor', [DatalayerController::class, 'subcontractor']);
    Route::post('/datalayer/subcontractor', [DatalayerController::class, 'subcontractor']);

    // Client registration
    Route::post('/clientreg', [DatalayerController::class, 'clientreg']);
    Route::post('/datalayer/clientreg', [DatalayerController::class, 'clientreg']);

    // Client billing
    Route::post('/clientbill', [DatalayerController::class, 'clientbill']);
    Route::post('/datalayer/clientbill', [DatalayerController::class, 'clientbill']);

    // Accounts payable
    Route::post('/payable', [DatalayerController::class, 'payable']);
    Route::post('/datalayer/payable', [DatalayerController::class, 'payable']);

    // Project registration
    Route::post('/project_register', [DatalayerController::class, 'projectRegister']);
    Route::post('/datalayer/project-register', [DatalayerController::class, 'projectRegister']);
    Route::post('/datalayer/project_register', [DatalayerController::class, 'projectRegister']);

    // Expense categories
    Route::get('/expense_categories', [DatalayerController::class, 'expenseCategories']);
    Route::get('/datalayer/expense-categories', [DatalayerController::class, 'expenseCategories']);
    Route::get('/datalayer/expense_categories', [DatalayerController::class, 'expenseCategories']);

    // Expense subcategories
    Route::get('/expense_sub_categories/{exp_id}', [DatalayerController::class, 'expenseSubCategories']);
    Route::get('/datalayer/expense-sub-categories/{exp_id}', [DatalayerController::class, 'expenseSubCategories']);
    Route::get('/datalayer/expense_sub_categories/{exp_id}', [DatalayerController::class, 'expenseSubCategories']);

    // Daily attendance
    Route::post('/daily_attendance', [DatalayerController::class, 'dailyAttendance']);
    Route::post('/datalayer/daily-attendance', [DatalayerController::class, 'dailyAttendance']);
    Route::post('/datalayer/daily_attendance', [DatalayerController::class, 'dailyAttendance']);

    // Vehicle cost
    Route::post('/vehicle_cost', [DatalayerController::class, 'vehicleCost']);
    Route::post('/datalayer/vehicle-cost', [DatalayerController::class, 'vehicleCost']);
    Route::post('/datalayer/vehicle_cost', [DatalayerController::class, 'vehicleCost']);

    // Apply leave
    Route::post('/apply_Leave', [DatalayerController::class, 'applyLeave']);
    Route::post('/datalayer/apply-leave', [DatalayerController::class, 'applyLeave']);
    Route::post('/datalayer/apply_Leave', [DatalayerController::class, 'applyLeave']);
});
