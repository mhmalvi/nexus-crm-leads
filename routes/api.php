<?php

use App\Http\Controllers\Api\ChecklistMailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeadController;
use App\Models\LeadDetails;

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
Route::get('/lead/{id}/course', [\App\Http\Controllers\Api\LeadCheckListController::class, 'courseInfo']);
Route::post('/lead/{id}/course/update', [\App\Http\Controllers\Api\LeadCheckListController::class, 'updateCourse']);


Route::post('/lead/add/amount', [LeadController::class, 'leadAddAmount']);
Route::post('/lead/add/call', [LeadController::class, 'leadAddCallHistory']);

Route::post('/lead/mail', [ChecklistMailController::class, 'leadMail']);

Route::post('/save-mail-template', [ChecklistMailController::class, 'save_mail_template']);

Route::get('/mail-templates', [ChecklistMailController::class, 'fetch_mail_templates']);

Route::get('/mail-templates/{id}', [ChecklistMailController::class, 'fetch_mail_templates_by_id']);

Route::get('/delete-mail-templates/{id}', [ChecklistMailController::class, 'delete_template']);

Route::post('/create-lead',[LeadController::class, 'create_lead']);

Route::post('/create-lead-from-form', [LeadController::class, 'create_lead_from_form']);

Route::post('/excel-read', [LeadController::class, 'uploadLeadExcel']);

Route::put('lead-update/{lead_id}',[LeadController::class, 'lead_update']);

Route::post('assign-sales-to-lead',[LeadController::class, 'sales_assign_to_lead']);

Route::post('course-details-by-client', [LeadController::class, 'course_details']);

Route::post('course-details-by-course-id', [LeadController::class, 'course_details_by_course_id']);

Route::post('delete-sales-employee-by-user-id', [LeadController::class, 'delete_sales_employee_by_user_id']);

Route::post('delete-lead-comments', [LeadController::class, 'delete_comment']);

Route::post('delete-amount-history', [LeadController::class, 'delete_amount_history']);

Route::get('campaign-wise-lead-percentage', [\App\Http\Controllers\Api\CampaignController::class, 'campaign_wise_lead_percentage']);