<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('/lead/scrap', [\App\Http\Controllers\Api\LeadScraper::class, 'dataScraper']);
Route::post('/lead/create', [\App\Http\Controllers\Api\LeadController::class, 'createLead']);
Route::post('/lead/list', [\App\Http\Controllers\Api\LeadController::class, 'leadList']);
Route::post('/lead/details', [\App\Http\Controllers\Api\LeadController::class, 'leadDetails']);
Route::post('/lead/filter', [\App\Http\Controllers\Api\LeadController::class, 'leadFilter']);

Route::put('/lead/status', [\App\Http\Controllers\Api\LeadController::class, 'leadStatusUpdate']);
Route::put('/lead/quality/update', [\App\Http\Controllers\Api\LeadController::class, 'leadQualityUpdate']);
Route::put('/lead/{lead_id}/update', [\App\Http\Controllers\Api\LeadController::class, 'leadUpdate']);
Route::post('/lead/assign', [\App\Http\Controllers\Api\LeadController::class, 'leadAssign']);

Route::post('/campaign/list', [\App\Http\Controllers\Api\CampaignController::class, 'campaignList']);

Route::post('/lead/checklist', [\App\Http\Controllers\Api\LeadCheckListController::class, 'index']);
Route::post('/lead/checklist/create', [\App\Http\Controllers\Api\LeadCheckListController::class, 'create']);
Route::post('/lead/checklist/add/document', [\App\Http\Controllers\Api\LeadCheckListController::class, 'addStudentDocuments']);
Route::post('/lead/checklist/student/documents', [\App\Http\Controllers\Api\LeadCheckListController::class, 'getStudentDocuments']);
Route::put('/lead/checklist/update', [\App\Http\Controllers\Api\LeadCheckListController::class, 'update']);
Route::post('/lead/checklist/delete', [\App\Http\Controllers\Api\LeadCheckListController::class, 'delete']);



Route::post('/lead/add/amount', [\App\Http\Controllers\Api\LeadController::class, 'leadAddAmount']);
Route::post('/lead/add/call', [\App\Http\Controllers\Api\LeadController::class, 'leadAddCallHistory']);
