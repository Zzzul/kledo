<?php

use App\Http\Controllers\ApprovalStageController;
use App\Http\Controllers\ApproverController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\StatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::patch('/expenses/{id}/approve', [ExpenseController::class, 'approve']);
Route::apiResource('/statuses', StatusController::class);
Route::apiResource('/approvers', ApproverController::class);
Route::apiResource('/approval-stages', ApprovalStageController::class);
Route::apiResource('/expenses', ExpenseController::class);
