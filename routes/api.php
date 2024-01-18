<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\LeadLocationColorController;

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

Route::post('/review/{lead_id}', [\App\Http\Controllers\Api\LeadController::class, 'single_comment']);
Route::post('/multi-review/{lead_id}', [\App\Http\Controllers\Api\LeadController::class, 'multi_comment']);

Route::post('/add-lead-location-color', [LeadLocationColorController::class, 'add_color']);
Route::get('/location-color', [LeadLocationColorController::class, 'getColor']);
Route::get('/delete-location-color', [LeadLocationColorController::class, 'deleteColor']);
Route::get('/update-location-color', [LeadLocationColorController::class, 'updateColor']);

Route::put('/lead/status', [\App\Http\Controllers\Api\LeadController::class, 'leadStatusUpdate']);
Route::get('/lead/lead_id={lead_id}/lead-status-logs',[\App\Http\Controllers\Api\LeadController::class,'lead_status_logs']);
Route::put('/lead/response', [\App\Http\Controllers\Api\LeadController::class, 'leadResponse']);
Route::put('/lead/quality/update', [\App\Http\Controllers\Api\LeadController::class, 'leadQualityUpdate']);
Route::put('/lead/{lead_id}/update', [\App\Http\Controllers\Api\LeadController::class, 'leadUpdate']);
Route::post('/lead/assign', [\App\Http\Controllers\Api\LeadController::class, 'leadAssign']);
Route::post('/lead/{id}/unassign-lead', [\App\Http\Controllers\Api\LeadController::class, 'unassign_lead']);

Route::post('/campaign/list', [\App\Http\Controllers\Api\CampaignController::class, 'campaignList']);

Route::post('/checklist-save', [\App\Http\Controllers\Api\ChecklistMailController::class, 'save_checklist']);
Route::post('/company_id={id}/checklist-delete', [\App\Http\Controllers\Api\ChecklistMailController::class, 'delete_checklist']);
Route::get('/company_id={id}/checklist-fetch', [\App\Http\Controllers\Api\ChecklistMailController::class, 'fetch_checklist']);


Route::post('/lead/checklist', [\App\Http\Controllers\Api\LeadCheckListController::class, 'index']);
Route::post('/lead/checklist/create', [\App\Http\Controllers\Api\LeadCheckListController::class, 'create']);
Route::post('/lead/checklist/add/document', [\App\Http\Controllers\Api\LeadCheckListController::class, 'addStudentDocuments']);
Route::post('/lead/checklist/student/documents', [\App\Http\Controllers\Api\LeadCheckListController::class, 'getStudentDocuments']);
Route::delete('/lead/checklist/{document_id}/delete/documents', [\App\Http\Controllers\Api\LeadCheckListController::class, 'removeStudentDocument']);
Route::put('/lead/checklist/update', [\App\Http\Controllers\Api\LeadCheckListController::class, 'update']);
Route::post('/lead/checklist/delete', [\App\Http\Controllers\Api\LeadCheckListController::class, 'delete']);

Route::get('/checklist_id={id}/view-pdf-content',[\App\Http\Controllers\Api\ChecklistMailController::class, 'pdf_viewer']);

Route::post('/add-course',[\App\Http\Controllers\Api\LeadController::class, 'add_course']);
Route::post('/add-course-by-accountant',[\App\Http\Controllers\Api\LeadController::class, 'add_course_by_accountant']);
Route::get('/course_id={course_id}/get-course-details-in-accountant',[\App\Http\Controllers\Api\LeadController::class,'get_course_details_in_accountant']);
Route::post('/course_id={course_id}/course-update-from-accountant',[\App\Http\Controllers\Api\LeadController::class,'update_course_details_from_accountant']);
Route::post('/course_id={course_id}/course-destroy-from-accountant',[\App\Http\Controllers\Api\LeadController::class,'destroy_course_from_accountant']);
Route::get('/get-course-in-accountant',[\App\Http\Controllers\Api\LeadController::class,'get_course_in_accountant']);
Route::get('/lead/courses', [\App\Http\Controllers\Api\LeadCheckListController::class, 'getCoursesList']);

Route::post('/lead/add/amount', [\App\Http\Controllers\Api\LeadController::class, 'leadAddAmount']);
Route::post('/lead/add/call', [\App\Http\Controllers\Api\LeadController::class, 'leadAddCallHistory']);

Route::post('/save-mail-template', [\App\Http\Controllers\Api\ChecklistMailController::class, 'save_mail_template']);
// Route::post('/destroy-mail-template',[\App\Http\Controllers\Api\ChecklistMailController::class,'destroy_mail_template']);
Route::post('/lead/mail', [\App\Http\Controllers\Api\ChecklistMailController::class, 'leadMail']);

Route::get('/assigned-lead-list/{id}', [\App\Http\Controllers\Api\SalesController::class, 'assigned_leads']);

// Route::get('/lead-list', [\App\Http\Controllers\Api\SalesController::class, 'lead_list']);

Route::get('/unassigned-lead-list/{id}', [\App\Http\Controllers\Api\SalesController::class, 'unassigned_leads']);

Route::get('/mail-templates', [\App\Http\Controllers\Api\ChecklistMailController::class, 'fetch_mail_templates']);

Route::get('/mail-templates/{id}', [\App\Http\Controllers\Api\ChecklistMailController::class, 'fetch_mail_templates_by_id']);

Route::post('/template_id={template_id}/delete-mail-templates', [\App\Http\Controllers\Api\ChecklistMailController::class, 'destroy_mail_template']);

Route::get('/lead/{id}/course', [\App\Http\Controllers\Api\LeadCheckListController::class, 'courseInfo']);
Route::post('/lead/{id}/course/update', [\App\Http\Controllers\Api\LeadCheckListController::class, 'updateCourse']);

Route::post('/create-lead',[\App\Http\Controllers\Api\LeadController::class, 'create_lead']);

Route::post('/create-lead-from-form', [\App\Http\Controllers\Api\LeadController::class, 'create_lead_from_form']);

Route::post('/excel-read', [\App\Http\Controllers\Api\LeadController::class, 'uploadLeadExcel']);

Route::put('lead-update/{lead_id}',[\App\Http\Controllers\Api\LeadController::class, 'lead_update']);

Route::post('assign-sales-to-lead',[\App\Http\Controllers\Api\LeadController::class, 'sales_assign_to_lead']);

Route::post('/assign-leads', [\App\Http\Controllers\Api\SalesController::class, 'assign_leads_to_sales']);

Route::post('/unassign-leads', [\App\Http\Controllers\Api\SalesController::class, 'unassign_leads']);

Route::get('/sales-list/{id}',[\App\Http\Controllers\Api\SalesController::class, 'sales_list']);

Route::post('/sales_id={sales_id}/company_id={company_id}/get-lead-list-in-sales',[\App\Http\Controllers\Api\SalesController::class,'lead_list_in_sales']);

Route::post('course-details-by-client',[\App\Http\Controllers\Api\LeadController::class, 'course_details']);

Route::post('course-details-by-course-id', [\App\Http\Controllers\Api\LeadController::class, 'course_details_by_course_id']);

Route::post('delete-sales-employee-by-user-id', [\App\Http\Controllers\Api\LeadController::class, 'delete_sales_employee_by_user_id']);

Route::post('delete-lead-comments', [\App\Http\Controllers\Api\LeadController::class, 'delete_comment']);

Route::post('delete-amount-history', [\App\Http\Controllers\Api\LeadController::class, 'delete_amount_history']);

Route::get('campaign-wise-lead-percentage', [\App\Http\Controllers\Api\CampaignController::class, 'campaign_wise_lead_percentage']);

Route::get('campaign-status-change', [\App\Http\Controllers\Api\CampaignController::class, 'campaign_status_change']);

Route::get('counts',[\App\Http\Controllers\Api\CountController::class, 'counts']);