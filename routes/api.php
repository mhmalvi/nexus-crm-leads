<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeadController;

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
Route::post('/lead/create', [LeadController::class, 'createLead']);
Route::post('/lead/list', [LeadController::class, 'leadList']);
Route::post('/lead/details', [LeadController::class, 'leadDetails']);
Route::post('/lead/filter', [LeadController::class, 'leadFilter']);

Route::post('/review/{lead_id}', [LeadController::class, 'single_comment']);
Route::post('/multi-review/{lead_id}', [LeadController::class, 'multi_comment']);

Route::put('/lead/status', [LeadController::class, 'leadStatusUpdate']);
Route::put('/lead/response', [LeadController::class, 'leadResponse']);
Route::put('/lead/quality/update', [LeadController::class, 'leadQualityUpdate']);
Route::put('/lead/{lead_id}/update', [LeadController::class, 'leadUpdate']);
Route::post('/lead/assign', [LeadController::class, 'leadAssign']);

Route::post('/campaign/list', [\App\Http\Controllers\Api\CampaignController::class, 'campaignList']);

Route::post('/lead/checklist', [\App\Http\Controllers\Api\LeadCheckListController::class, 'index']);
Route::post('/lead/checklist/create', [\App\Http\Controllers\Api\LeadCheckListController::class, 'create']);
Route::post('/lead/checklist/add/document', [\App\Http\Controllers\Api\LeadCheckListController::class, 'addStudentDocuments']);
Route::post('/lead/checklist/student/documents', [\App\Http\Controllers\Api\LeadCheckListController::class, 'getStudentDocuments']);
Route::delete('/lead/checklist/{document_id}/delete/documents', [\App\Http\Controllers\Api\LeadCheckListController::class, 'removeStudentDocument']);
Route::put('/lead/checklist/update', [\App\Http\Controllers\Api\LeadCheckListController::class, 'update']);
Route::post('/lead/checklist/delete', [\App\Http\Controllers\Api\LeadCheckListController::class, 'delete']);

Route::get('/lead/courses', [\App\Http\Controllers\Api\LeadCheckListController::class, 'getCoursesList']);


Route::post('/lead/add/amount', [LeadController::class, 'leadAddAmount']);
Route::post('/lead/add/call', [LeadController::class, 'leadAddCallHistory']);

Route::post('/create-lead',[LeadController::class, 'create_lead']);