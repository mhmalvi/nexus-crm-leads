<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CampaignDetails;
use App\Models\CoursesInfo;
use App\Models\LeadAmountHistory;
use App\Mail\NewLeadMail;
use App\Mail\StatusChange;
use App\Mail\Response;
use App\Models\LeadCallHistory;
use App\Models\LeadDetails;
use App\Imports\LeadsImport;
use App\Models\LeadMultiComment;
use App\Models\LeadSalesEmployee;
use App\Models\LeadStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Count;

class LeadController extends Controller
{
    /**
     * Create Lead
     * @param Request $request
     * @return
     */
    public function get_course_in_accountant(Request $request)
    {
        if ($request->bearerToken()) {
            $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
            $flag_receive = $flag['data'];
            if ($flag_receive == 1) {
                $courses = CoursesInfo::orderBy('id', 'desc')->get();
                if ($courses) {
                    return response()->json([
                        'message' => 'success',
                        'status' => 200,
                        'data' => $courses
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'failed',
                        'status' => 500
                    ], 500);
                }
            } else {
                return response()->json([
                    'message' => 'Unauthenticated',
                    'status' => 401
                ], 401);
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated',
                'status' => 401
            ], 401);
        }
    }

    public function leadAddAmount(Request $request)
    {

        if (!isset($request->lead_id) || !isset($request->amount)) {
            return response()->json([
                'status' => false,
                'message' => 'Lead id and Lead Amount required'
            ], 406);
        }


        try {

            LeadAmountHistory::updateOrcreate([
                'lead_id' => $request->lead_id,
                'amount' => $request->amount
            ])->toArray();
            $leadAmountHistory = LeadAmountHistory::where('lead_id', '=', $request->lead_id)->get()->toArray();
            return response()->json([
                'status' => true,
                'message' => 'Lead amount added successfully',
                'data' => $leadAmountHistory

            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function leadAddCallHistory(Request $request)
    {

        if (!isset($request->lead_id) || !isset($request->call_start_time) || !isset($request->call_end_time)) {
            return response()->json([
                'status' => false,
                'message' => 'Lead id and Call start and end time required'
            ], 406);
        }

        $callStartTime = Carbon::parse($request->call_start_time)->toDateTimeString();
        $callEndTime = Carbon::parse($request->call_end_time)->toDateTimeString();
        try {

            LeadCallHistory::updateOrcreate([
                'lead_id' => $request->lead_id,
                'call_start_time' => $callStartTime,
                'call_end_time' => $callEndTime,
                'call_remark' => $request->call_remark
            ])->toArray();

            $count = DB::table('lead_details')->where('lead_id', $request->lead_id)->first();
            // dd($count);
            DB::table('lead_details')->where('lead_id', $request->lead_id)->update(['call_count' => $count->call_count + 1]);


            $leadCallHistory = LeadCallHistory::where('lead_id', '=', $request->lead_id)->get()->toArray();

            return response()->json([
                'status' => true,
                'message' => 'Lead Call history added successfully',
                'data' => $leadCallHistory
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /////////////////////////// update course details from accountant ////////////////////
    public function get_course_details_in_accountant(Request $request, $course_id)
    {
        // dd("helllo");
        if ($request->bearerToken()) {
            $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
            $flag_receive = $flag['data'];
            if ($flag_receive == 1) {
                $course = CoursesInfo::find($course_id);
                if ($course) {
                    return response()->json([
                        'message' => 'success',
                        'status' => 200,
                        'data' => $course
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'failed',
                        'status' => 500
                    ], 500);
                }
            } else {
                return response()->json([
                    'message' => 'Unauthenticated',
                    'status' => 401
                ], 401);
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated',
                'status' => 401
            ], 401);
        }
    }

    /////////////////// delete course by accountant //////////////
    public function destroy_course_from_accountant(Request $request, $course_id)
    {
        if ($request->bearerToken()) {
            $userApi = env('USER_SERVICE_API', '');
            $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
            $flag_receive = $flag['data'];
            if ($flag_receive == 1) {
                $course = CoursesInfo::find($course_id);
                if ($course) {
                    if ($course->checklist_path != null) {
                        unlink(public_path($course->checklist_path));
                    }

                    $response = $course->delete();
                    if ($response) {
                        return response()->json([
                            'message' => 'Deleted',
                            'status' => 200
                        ], 200);
                    }
                }
            } else {
                return response()->json([
                    'message' => 'Unauthenticated',
                    'status' => 401
                ], 401);
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated',
                'status' => 401
            ], 401);
        }
    }

    public function update_course_details_from_accountant(Request $request, $course_id)
    {
        if ($request->bearerToken()) {
            $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
            $flag_receive = $flag['data'];
            if ($flag_receive == 1) {
                // dd($request->checklist);
                $course_exist = CoursesInfo::find($course_id);
                if ($course_exist->checklist_path !== null && isset($request->checklist)) {
                    unlink(public_path($course_exist->checklist_path));
                }


                if (isset($request->checklist)) {
                    $fileName = time() . '.' . $request->checklist->getClientOriginalExtension();
                    $request->checklist->move(public_path('assets/course_checklist'), $fileName);
                    $file_path = "assets/course_checklist/" . $fileName;
                }
                $course = CoursesInfo::where('id', $course_id)->update([
                    'course_code' => $request->course_code,
                    'course_title' => $request->course_title,
                    'course_description' => $request->course_description,
                    'checklist_name' => isset($request->checklist) ? $request->checklist->getClientOriginalName() : $course_exist->checklist_name,
                    'checklist_path' => isset($file_path) ? $file_path : $course_exist->checklist_path
                ]);
                if ($course == 1) {
                    return response()->json([
                        'message' => 'Course updated',
                        'status' => 201
                    ], 201);
                } else {
                    return response()->json([
                        'message' => 'Course not found',
                        'status' => 404
                    ], 404);
                }
            } else {
                return response()->json([
                    'message' => 'Unauthenticated',
                    'status' => 401
                ], 401);
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated',
                'status' => 401
            ], 401);
        }
    }

    public function leadAssign(Request $request)
    {

        if (!isset($request->lead_id) || !isset($request->sales_user_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Lead id and Sales Id required'
            ], 406);
        }

        try {
            $leadDetails = LeadDetails::where('lead_id', '=', $request->lead_id)->first();
            $leadDetails->sales_user_id = $request->sales_user_id;
            $leadDetails->assigned_sales_history =
                $leadDetails->save();
            LeadSalesEmployee::updateOrcreate([
                'sales_user_id' => $request->sales_user_id,
                'lead_id' => $request->lead_id,
                'assign_by' => $request->assign_by
            ])->toArray();
            $leadSalesEmployeeHistory = LeadSalesEmployee::where('lead_id', '=', $request->lead_id)->get()->toArray();
            return response()->json([
                'status' => true,
                'message' => 'Lead Sales Employee added successfully',
                'data' => $leadSalesEmployeeHistory
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function lead_status_logs(Request $request, $lead_id)
    {
        // if ($request->bearerToken()) {
        //     $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
        //     $flag_receive = $flag['data'];
        //     if ($flag_receive == 1) {
        $lead_status = LeadStatus::where('lead_id', $lead_id)->get();
        $lead_data = [];
        for ($i = 0; $i < count($lead_status); $i++) {
            if ($lead_status[$i]->updated_by != null) {
                $user_name = DB::connection('user')->table('user_profile')->where('user_id', $lead_status[$i]->updated_by)->first();
                if ($lead_status[$i]->updated_by == $user_name->user_id) {
                    $lead_status[$i]->selected_by = $user_name->full_name;
                }
            } else {
                $lead_status[$i]->selected_by = null;
            }
        }
        $lead_status = LeadStatus::where('lead_id', $lead_id)->get();
        $lead_data = [];
        for ($i = 0; $i < count($lead_status); $i++) {
            if ($lead_status[$i]->updated_by != null) {
                $user_name = DB::connection('user')->table('user_profile')->where('user_id', $lead_status[$i]->updated_by)->first();
                if ($lead_status[$i]->updated_by == $user_name->user_id) {
                    $lead_status[$i]->selected_by = $user_name->full_name;
                }
            } else {
                $lead_status[$i]->selected_by = null;
            }
        }

        if ($lead_status) {
            return response()->json([
                'message'    => 'success',
                'status' => 200,
                'data' => $lead_status
            ], 200);
        } else {
            return response()->json([
                'message'    => 'Failed',
                'status' => 500
            ], 500);
        }
        if ($lead_status) {
            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $lead_status
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed',
                'status' => 500
            ], 500);
        }
        //     } else {
        //         return response()->json([
        //             'message' => 'Unauthenticated',
        //             'status' => 401
        //         ], 401);
        //     }
        // } else {
        //     return response()->json([
        //         'message' => 'Unauthenticated',
        //         'status' => 401
        //     ], 401);
        // }
    }

    public function unassign_lead(Request $request, $id)
    {
        // if ($request->bearerToken()) {
        //     $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
        //     $flag_receive = $flag['data'];
        //     if ($flag_receive == 1) {
        $leadDetails = LeadDetails::where('lead_id', $id)->first();
        $leadDetails->sales_user_id = 0;
        $update = $leadDetails->save();
        if ($update) {
            return response()->json([
                'message' => 'Lead unassigned',
                'status' => 201
            ], 201);
        } else {
            return response()->json([
                'message' => 'failed',
                'status' => 500
            ], 500);
        }
        //     } else {
        //         return response()->json([
        //             'message' => 'Unauthenticated',
        //             'status' => 401
        //         ], 401);
        //     }
        // } else {
        //     return response()->json([
        //         'message' => 'Unauthenticated',
        //         'status' => 401
        //     ], 401);
        // }
    }
    public function add_course_by_accountant(Request $request)
    {
        if ($request->bearerToken()) {
            $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
            $flag_receive = $flag['data'];
            if ($flag_receive == 1) {
                if ($request->course_code && $request->course_title && $request->course_description) {
                    $course = CoursesInfo::where('course_code', $request->course_code)->exists();
                    if ($course) {
                        return response()->json([
                            'message' => 'Course already exists',
                            'status' => 403
                        ], 403);
                    } else {
                        $fileName = time() . '.' . $request->checklist->getClientOriginalExtension();
                        $request->checklist->move(public_path('assets/course_checklist'), $fileName);
                        $file_path = "assets/course_checklist/" . $fileName;

                        $save = new CoursesInfo();
                        $save->course_code = $request->course_code;
                        $save->course_title = $request->course_title;
                        $save->course_description = $request->course_description;
                        $save->checklist_name = $request->checklist->getClientOriginalName();
                        $save->checklist_path = $file_path;
                        $save->status = 1;
                        $save->save();

                        $course_id = $save->id;
                        $user_id = $request->user_id;

                        if ($save) {
                            return response()->json([
                                'message' => 'Course saved',
                                'status' => 201,
                                'data' => $save
                            ], 201);
                        }
                        // }
                    }
                } else {
                    return response()->json([
                        'message' => 'Please insert all fields',
                        'status' => 'empty'
                    ], 500);
                }
            }
        }
    }

    public function leadQualityUpdate(Request $request)
    {

        if (!isset($request->lead_id) && !isset($request->sales_user_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Lead id and Sales Id required '
            ], 406);
        }


        try {

            $leadDetails = LeadDetails::where('lead_id', '=', $request->lead_id)->first();
            if ($leadDetails == "") {
                return response()->json([
                    'status' => false,
                    'message' => 'Lead details Not found',
                    'data' => $request->lead_id
                ], 404);
            }

            if ($leadDetails->sales_user_id > 0) {
                if (isset($request->star_review))
                    $leadDetails->star_review = (int) $request->star_review;
                if (isset($request->lead_remarks))
                    $leadDetails->lead_remarks = $request->lead_remarks;
                $leadDetails->save();
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Lead Not Assign yet ',
                    'data' => $request->lead_id
                ], 401);
            }

            return response()->json([
                'status' => true,
                'message' => 'Lead update successfully',
                'lead' => $leadDetails->toArray()

            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function single_comment(Request $request, $lead_id)
    {
        $single_comment = LeadDetails::where('lead_id', $lead_id)->first();
        $single_comment->lead_remarks = $request->remarks;
        $save = $single_comment->save();
        if ($save) {
            return response()->json([
                'status' => 200,
                'message' => 'success'
            ], 200);
        } else {
            return response()->json([
                'status' => 424,
                'message' => 'failed'
            ]);
        }
    }

    public function multi_comment(Request $request, $lead_id)
    {
        $request->validate([
            'comments' => 'required'
        ]);
        $multiComment = new LeadMultiComment();

        if ($request->comments !== "") {
            $multiComment->comments = $request->comments;
            $multiComment->lead_id = $lead_id;
            $save = $multiComment->save();
            $comments = LeadMultiComment::where('lead_id', $lead_id)->get();
            if ($save) {
                return response()->json([
                    'status' => 200,
                    'message' => 'success',
                    'data' => $comments
                ], 200);
            } else {
                return response()->json([
                    'status' => 424,
                    'message' => 'failed'
                ], 424);
            }
        } else {
            return response()->json([
                'status' => 424,
                'message' => 'column name should be comments'
            ], 424);
        }
    }

    public function create_lead(Request $request)
    {
        // if ($request->bearerToken()) {

        //     $userApi = env('USER_SERVICE_API', '');
        //     $flag = Http::withToken($request->bearerToken())->post($userApi . '/check-if-token-exists');
        //     $flag_receive = $flag['data'];
        //     if ($flag_receive == 1) {
        $id = round(microtime(true) * 1000);
        $lead_id = intval($id);
        $living_place = [
            "name"
            => "what_state_do_you_live_in?",
            "values" => $request->living_place
        ];

        $lead_status = 1;
        $companyApi = env('COMPANY_SERVICE_API', '');
        $logo_details_of_logo = HTTP::get($companyApi . '/documents-details/' . $request->client_id);
        // dd(json_encode($logo_details_of_logo));
        $logo_response_of_logo = json_decode($logo_details_of_logo->body());
        // dd($logo_response_of_logo);
        $client_name = $logo_response_of_logo->client;
        // dd($client_name);
        $existing_lead = LeadDetails::where('lead_id', $lead_id)->first();
        $course = CoursesInfo::where('id', $request->course_id)->first();
        $logo = $logo_response_of_logo->data->document_name;
        if (!$existing_lead) {
            $save = LeadDetails::create([
                'lead_id' => $lead_id,
                'student_id' => 0,
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number,
                'student_email' => $request->student_email,
                'client_id' => $request->client_id,
                'campaign_id' => 0,
                'sales_user_id' => 0,
                'document_certificate_id' => 0,
                'course_id' => $request->course_id,
                'work_location' => $request->work_location,
                'lead_from' => $request->lead_from,
                'form_data' => $request->form_data,
                'star_review' => 0,
                'lead_apply_date' => Carbon::now(),
                'lead_details_status' => 1
            ]);
            // HTTP::post('http://localhost:2000/api/send-mail', ['name' => $request->full_name, 'lead_status' => 1]);
            // HTTP::post('https://crm-mailer.onrender.com/api/send-mail', ['name' => $request->full_name, 'lead_status' => $lead_status, 'logo' => $logo, 'client' => $client_name, 'course' => $course->course_title]);
            Mail::to($request->student_email)->queue(new NewLeadMail($request->full_name, $lead_status, $logo, $client_name, $course->course_title, $client_name));
            if ($save) {
                return response()->json([
                    'message' => 'success',
                    'status' => 201,
                    'data' => $save
                ], 200);
            } else {
                abort(500);
            }
        } else {
            return response()->json([
                'message' => 'exists',
                'status' => 403
            ], 403);
        }
        //     } else {
        //         return response()->json([
        //             'message' => 'Unauthenticated',
        //             'status' => 401
        //         ], 401);
        //     }
        // } else {
        //     return response()->json([
        //         'message' => 'Unauthenticated',
        //         'status' => 401
        //     ], 401);
        // }
    }

    public function createLead(Request $request)
    {

        if (!isset($request->client_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Client id required',
                'data' => $request->client_id
            ], 406);
        }

        try {

            return response()->json([
                'status' => true,
                'message' => 'Lead Created Successfully',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Lead List
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function leadList(Request $request)
    {
        try {
            if ($request->bearerToken()) {
                $flag = Http::crm_user()->withToken($request->bearerToken())->post('/check-if-token-exists');
                $flag_receive = $flag['data'];
                if ($flag_receive == 1) {
                    if ($request->role_id == 5) {
                        $data = DB::table('lead_details')
                            ->select(
                                'lead_details.id as lid',
                                'lead_details.lead_id  as lead_id',
                                'lead_details.student_id as student_id',
                                'lead_details.full_name as full_name',
                                'lead_details.phone_number as phone_number',
                                'lead_details.student_email as student_email',
                                'lead_details.client_id as client_id',
                                'lead_details.campaign_id as campaign_id',
                                'lead_details.sales_user_id as sales_user_id',
                                'lead_details.document_certificate_id as document_certificate_id',
                                'lead_details.course_id as course_id',
                                'lead_details.work_location as work_location',
                                'lead_details.lead_from as lead_from',
                                'lead_details.form_data as form_data',
                                'lead_details.star_review as star_review',
                                'lead_details.lead_apply_date as lead_apply_date',
                                'lead_details.lead_remarks as lead_remarks',
                                'lead_details.lead_details_status as lead_details_status',
                                'lead_details.created_at as created_at',
                                'lead_details.updated_at as updated_at',
                                'courses_info.id as cid',
                                'courses_info.course_code as course_code',
                                'courses_info.course_title as course_title',
                                'courses_info.course_description as course_description',
                                'courses_info.status as status'
                            )->where('lead_details.sales_user_id', $request->user_id)
                            ->leftJoin('courses_info', function ($join) {
                                $join->on('lead_details.course_id', '=', 'courses_info.id');
                            })->get();
                        if (isset($request->client_id))
                            $data = $data->where('sales_user_id', $request->user_id)->where('lead_details.client_id', '=', $request->client_id)->orderBy('lead_details.lead_apply_date', 'desc');

                        if (isset($request->student_id))
                            $data = $data->where('sales_user_id', $request->user_id)->where('lead_details.student_id', '=', $request->student_id)->orderBy('lead_details.lead_apply_date', 'desc');

                        $paginate_data = $data->get();
                        $lead_id = LeadDetails::select('lead_id')->get();
                        for ($i = 0; $i < count($lead_id); $i++) {
                            if (isset($lead_id[$i])) {
                                $sales_objects[] = LeadSalesEmployee::where('lead_id', $lead_id[$i]->lead_id)->get();
                            }
                        }

                        for ($j = 0; $j < count($paginate_data); $j++) {
                            for ($k = 0; $k < count($sales_objects); $k++) {
                                if (isset($sales_objects[$k][0]->lead_id)) {
                                    for ($m = 0; $m < count($sales_objects[$k]); $m++) {
                                        if ($paginate_data[$j]->lead_id == $sales_objects[$k][$m]->lead_id) {
                                            $paginate_data[$j]->assignedHistory[] = $sales_objects[$k][$m]->sales_user_id;
                                        }
                                    }
                                }
                            }
                        }
                        return response()->json([
                            'status' => 200,
                            'message' => 'All Lead List',
                            'data' => $paginate_data,
                        ], 200);
                    } else {
                        $data = DB::table('lead_details')
                            ->select(
                                'lead_details.id as lid',
                                'lead_details.lead_id  as lead_id',
                                'lead_details.student_id as student_id',
                                'lead_details.full_name as full_name',
                                'lead_details.phone_number as phone_number',
                                'lead_details.student_email as student_email',
                                'lead_details.client_id as client_id',
                                'lead_details.campaign_id as campaign_id',
                                'lead_details.sales_user_id as sales_user_id',
                                'lead_details.document_certificate_id as document_certificate_id',
                                'lead_details.course_id as course_id',
                                'lead_details.work_location as work_location',
                                'lead_details.lead_from as lead_from',
                                'lead_details.form_data as form_data',
                                'lead_details.star_review as star_review',
                                'lead_details.lead_apply_date as lead_apply_date',
                                'lead_details.lead_remarks as lead_remarks',
                                'lead_details.lead_details_status as lead_details_status',
                                'lead_details.created_at as created_at',
                                'lead_details.updated_at as updated_at',
                                'lead_details.call_count as call_count',
                                'courses_info.id as cid',
                                'courses_info.course_code as course_code',
                                'courses_info.course_title as course_title',
                                'courses_info.course_description as course_description',
                                'courses_info.status as status'
                            )->leftJoin('courses_info', function ($join) {
                                $join->on('lead_details.course_id', '=', 'courses_info.id');
                            });
                        // ->leftJoin('counts', 'lead_details.lead_id', '=', 'counts.lead_id');
                        if (isset($request->client_id))
                            $data = $data->where('lead_details.client_id', '=', $request->client_id)->orderBy('lead_details.lead_apply_date', 'desc')->get();

                        if (isset($request->student_id))
                            $data = $data->where('lead_details.student_id', '=', $request->student_id)->orderBy('lead_details.lead_apply_date', 'desc')->get();
                        return response()->json([
                            'status' => 200,
                            'message' => 'All Lead List',
                            'data' => $data,
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'message' => 'unauthorized',
                        'status' => 401
                    ], 401);
                }
            } else {
                return response()->json([
                    'message' => 'unauthorized',
                    'status' => 401
                ], 401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function lead_update(Request $request, $lead_id)
    {
        $lead = LeadDetails::where('lead_id', $lead_id)->first();
        if ($lead) {
            $lead->phone_number = $request->contact;
            $lead->student_email = $request->email;
            $lead->course_id = $request->course_id;
            $lead->work_location = $request->work_location;

            $lead->save();
            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => [$lead]
            ], 200);
        } else {
            return response()->json([
                'message' => 'lead id not found',
                'status' => 404,
                'data' => $lead_id
            ], 404);
        }
    }

    public function uploadLeadExcel(Request $request)
    {
        // if ($request->bearerToken()) {
        //     $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
        //     $flag_receive = $flag['data'];
        //     if ($flag_receive == 1) {
        $client_id = $request->client_id;
        $lead_import = new LeadsImport($client_id);
        $data = \Excel::import($lead_import, $request->file);
        if ($lead_import->flag == 1) {
            return response()->json([
                'message' => 'success',
                'status' => 200
            ]);
        } elseif ($lead_import->flag == 0) {
            return response()->json([
                'message' => 'Please reformat excel sheet columns',
                'status' => 400
            ], 400);
        } elseif ($lead_import->flag == 3) {
            return response()->json([
                'message' => 'Data already exists',
                'status' => 403
            ], 403);
        }
        //     } else {
        //         return response()->json([
        //             'message' => 'Unauthenticated',
        //             'status' => 401
        //         ], 401);
        //     }
        // } else {
        //     return response()->json([
        //         'message' => 'Unauthenticated',
        //         'status' => 401
        //     ], 401);
        // }
    }

    public function sales_assign_to_lead(Request $request)
    {
        // if ($request->bearerToken()) {
        //     $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
        //     $flag_receive = $flag['data'];
        //     if ($flag_receive == 1) {
        $lead_id = LeadDetails::select('lead_id')->where('course_id', $request->course_id)->where('client_id', $request->client_id)->get();
        $sales = explode(',', $request->sales_id);
        dd(json_decode($lead_id));
        for ($i = 0; $i < count($sales); $i++) {
            for ($j = 0; $j < count($lead_id); $j++) {
                $data = LeadSalesEmployee::create([
                    "sales_user_id" => $sales[$i],
                    "lead_id" => $lead_id[$j]->lead_id,
                    "active_status" => 1,
                    "assign_by" => $request->assigned_by
                ]);
            }
        }
        if ($data) {
            return response()->json([
                'message' => 'success',
                'status' => 201,
                'data' => $data
            ], 201);
        } else {
            return response()->json([
                'message' => 'failed',
                'status' => 500
            ], 500);
        }
        //     } else {
        //         return response()->json([
        //             'message' => 'Unauthenticated',
        //             'status' => 401
        //         ], 401);
        //     }
        // } else {
        //     return response()->json([
        //         'message' => 'Unauthenticated',
        //         'status' => 401
        //     ], 401);
        // }
    }

    public function add_course(Request $request)
    {    //////////// insert course ///////////
        // if ($request->bearerToken()) {
        //     $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
        //     $flag_receive = $flag['data'];
        //     if ($flag_receive == 1) {
        if ($request->course_code && $request->course_title && $request->course_description) {
            $course = CoursesInfo::where('course_code', $request->course_code)->exists();
            if ($course) {
                return response()->json([
                    'message' => 'Course already exists',
                    'status' => 403
                ], 403);
            } else {
                $save = CoursesInfo::create([
                    'course_code' => $request->course_code,
                    'course_title' => $request->course_title,
                    'course_description' => $request->course_description,
                    'status' => 1
                ]);
                if ($save) {
                    return response()->json([
                        'message' => 'Course saved',
                        'status' => 201,
                        'data' => $save
                    ], 201);
                }
            }
        } else {
            return response()->json([
                'message' => 'Please insert all fields',
                'status' => 'empty'
            ], 500);
        }
        // }
        // }
    }

    public function course_details_by_course_id(Request $request)
    {
        if ($request->client_id !== "" || $request->course_id !== "") {
            $course_id_in_lead_details = LeadDetails::where('course_id', $request->course_id)->first();
            $course_id = $request->course_id;
            if ($course_id_in_lead_details) {
                try {
                    $data = DB::table('lead_details')
                        ->select(
                            'lead_sales_employee.sales_user_id as sales_id'
                        )
                        ->leftJoin('lead_sales_employee', function ($join) {
                            $join->on('lead_details.lead_id', '=', 'lead_sales_employee.lead_id');
                        })
                        ->where('course_id', $request->course_id)
                        ->where('client_id', $request->client_id)
                        ->where('lead_sales_employee.sales_user_id', '!=', null)
                        ->groupBy('lead_sales_employee.sales_user_id')
                        ->get();
                    if (!$data->isEmpty()) {
                        return response()->json([
                            'status' => 200,
                            'message' => 'All Lead List',
                            'data' => $data,
                        ], 200);
                    } else {
                        return response()->json([
                            'message' => 'Lead List not exists',
                            'status' => 404
                        ], 404);
                    }
                } catch (\Throwable $th) {
                    return response()->json([
                        'status' => false,
                        'message' => $th->getMessage()
                    ], 500);
                }
            } else {
                return response()->json([
                    'message' => "not found",
                    'status' => 404
                ]);
            }
        } else {
            return response()->json([
                'message' => "please enter course id or client id",
                'status' => 400
            ], 400);
        }
    }

    public function course_details(Request $request)
    {
        // if ($request->bearerToken()) {
        //     // dd($userApi);
        //     $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
        //     $flag_receive = $flag['data'];
        //     if ($flag_receive == 1) {
        $course_details = CoursesInfo::orderBy('id', 'desc')->get();
        // }
        if ($course_details) {
            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $course_details
            ]);
        } else {
            return response()->json([
                'message' => 'not found',
                'status' => 404,
            ]);
        }
        //     } else {
        //         return response()->json([
        //             'message' => 'unauthenticated',
        //             'status' => 401
        //         ], 401);
        //     }
        // } else {
        //     return response()->json([
        //         'message' => 'unauthenticated',
        //         'status' => 401
        //     ], 401);
        // }
    }

    /**
     * Lead Details
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse Details, Lead Status
     */
    public function leadDetails(Request $request)
    {
        // dd("hello");
        if (!isset($request->lead_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Lead id required'
            ], 406);
        }

        $leadId = $request->lead_id;

        try {
            $leadDetails = DB::table('lead_details')
                ->select(
                    'lead_details.id as lid',
                    'lead_details.lead_id  as lead_id',
                    'lead_details.lead_remarks as comment',
                    'lead_details.student_id as student_id',
                    'lead_details.full_name as full_name',
                    'lead_details.phone_number as phone_number',
                    'lead_details.student_email as student_email',
                    'lead_details.client_id as client_id',
                    'lead_details.campaign_id as campaign_id',
                    'lead_details.sales_user_id as sales_user_id',
                    'lead_details.document_certificate_id as document_certificate_id',
                    'lead_details.course_id as course_id',
                    'lead_details.work_location as work_location',
                    'lead_details.lead_from as lead_from',
                    'lead_details.form_data as form_data',
                    'lead_details.star_review as star_review',
                    'lead_details.lead_apply_date as lead_apply_date',
                    'lead_details.lead_remarks as lead_remarks',
                    'lead_details.lead_details_status as lead_details_status',
                    'lead_details.call_count as call_count',
                    'lead_details.created_at as created_at',
                    'lead_details.updated_at as updated_at',
                    'courses_info.id as cid',
                    'courses_info.course_code as course_code',
                    'courses_info.course_title as course_title',
                    'courses_info.course_description as course_description',
                    'courses_info.status as status'
                )
                ->leftJoin('courses_info', function ($join) {
                    $join->on('lead_details.course_id', '=', 'courses_info.id');
                })->where('lead_details.lead_id', '=', $leadId)
                ->get();

            if (count($leadDetails) == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lead details Not found'
                ], 404);
            }

            $leadAllStatus = LeadStatus::where('lead_id', '=', $leadId)->where('is_active', 1)->get();
            $isData = false;
            $lead_status_response = LeadStatus::where('lead_id', '=', $leadId)->where('lead_status', 3)->first();
            $multi_comments = LeadMultiComment::where('lead_id', $leadId)->orderBy('created_at', 'desc')->get();
            $multi_comment = array();
            foreach ($multi_comments as $comment) {
                $multi_comment = $comment;
            }
            if ($leadAllStatus != "") {

                foreach ($leadAllStatus as $leadAStatus) {
                    if ($leadAStatus['lead_status'] == '1') {
                        $isData = true;
                        break;
                    }
                }
            }
            if ($isData) {
                $leadAllStatus = $leadAllStatus->toArray();
            } else {

                $leadAllStatus = new LeadStatus;
                $leadAllStatus->lead_status = 1;
                $leadAllStatus->lead_id = $leadId;
                $leadAllStatus->created_at = $leadDetails[0]->lead_apply_date;
                $leadAllStatus->save();
                $leadAllStatus = $leadAllStatus->toArray();
            }
            $lead_all_status = LeadStatus::where('lead_id', $leadId)->where('is_active', 1)->get();

            $leadDetails[0]->form_data = json_decode($leadDetails[0]->form_data);
            $leadAmountHistory = LeadAmountHistory::where('lead_id', '=', $request->lead_id)->orderBy('id', 'desc')->get()->toArray();
            $leadCallHistory = LeadCallHistory::where('lead_id', '=', $request->lead_id)->orderBy('id', 'desc')->get()->toArray();
            $leadSalesEmployeeHistory = LeadSalesEmployee::where('lead_id', '=', $request->lead_id)->orderBy('id', 'desc')->get()->toArray();

            $paymentServiceAPI = env('PAYMENT_SERVICE_API', '');
            $paymentHistories = '';
            $salesUserIds = [];
            $salesEmployeDetails = '';
            $salesUserList = [];

            if ($leadSalesEmployeeHistory != "") {
                foreach ($leadSalesEmployeeHistory as $value) {
                    $salesUserIds[] = $value['sales_user_id'];
                    $salesUserIds[] = $value['assign_by'];
                }
                // $userServiceAPI = env('USER_SERVICE_API', '');
                $response = Http::crm_user()->post('/user/list', [
                    'users' => json_encode($salesUserIds)
                ]);


                $salesEmployeDetails = isset(json_decode($response->body())->data) ? json_decode($response->body())->data : '';
                $salesEmployeDetailsArray = [];
                if ($salesEmployeDetails != "") {
                    foreach ($salesEmployeDetails as $row) {
                        $salesEmployeDetailsArray[$row->user_id] = $row;
                    }
                    $temp = [];
                    foreach ($leadSalesEmployeeHistory as $salesUser) {
                        $temp['sales_user_name'] = isset($salesEmployeDetailsArray[$salesUser['sales_user_id']]->full_name) ? $salesEmployeDetailsArray[$salesUser['sales_user_id']]->full_name : '';
                        $temp['assignee_user_name'] = isset($salesEmployeDetailsArray[$salesUser['assign_by']]->full_name) ? $salesEmployeDetailsArray[$salesUser['assign_by']]->full_name : '';
                        $temp['sales_user_id'] = $salesUser['sales_user_id'];
                        $temp['assign_by'] = $salesUser['assign_by'];
                        $temp['created_at'] = $salesUser['created_at'];

                        array_push($salesUserList, $temp);
                    }
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'All Lead List',
                'leadDetails' => $leadDetails[0],
                'leadComments' => $multi_comments,
                'leadAllStatus' => $lead_all_status,
                'leadCallHistory' => $leadCallHistory,
                'leadAmountHistory' => $leadAmountHistory,
                'leadSalesEmployeeHistory' => $salesUserList,

            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function leadResponse(Request $request)
    {
        $lead_info = LeadDetails::where('lead_id', '=', $request->lead_id)->first();
        $lead_email = $lead_info->student_email;
        $name = $lead_info->full_name;
        $student_id = $lead_info->student_id;
        $lead_status = LeadStatus::where('lead_id', $request->lead_id)->where('lead_status', '=', 3)->first();
        if ($lead_status->lead_status == 3) {
            if (!$lead_status) {
                return response()->json([
                    'message' => "Lead ID not found",
                    'status' => 404,

                ], 404);
            } else {
                $lead_status->response = $request->response;
                $lead_status->lead_id = $request->lead_id;
                $save = $lead_status->save();
                $data = [
                    'lead_status' => $request->lead_status,
                    'lead_id' => $request->lead_id,
                    'response' => $request->response,
                    'to' => $lead_email,
                    'name' => $name,
                    'student_id' => $student_id
                ];
                $college = DB::connection('company')->table('companies')->where('id', $request->client_id)->first();
                $college_name = $college->name;

                if ($save) {
                    Mail::to($lead_email)->queue(new Response($request->lead_status, $college_name, $request->course, $name, $request->response));
                    return response()->json([
                        'message' => "success",
                        'status' => 200,
                        'data' => $data
                    ], 200);
                } else {
                    return response()->json([
                        'message' => "failed",
                        'status' => 500,
                        'data' => $request->all()
                    ], 500);
                }
            }
        }
    }

    public function delete_sales_employee_by_user_id(Request $request)
    {
        // if ($request->bearerToken()) {
        //     $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
        //     $flag_receive = $flag['data'];
        //     if ($flag_receive == 1) {
        try {
            if ($request->sales_user_id) {
                $data = LeadSalesEmployee::where('sales_user_id', $request->sales_user_id)->get();
                if ($data) {
                    foreach ($data as $datas) {
                        $delete = $datas->delete();
                    }
                    $leads = LeadDetails::where('sales_user_id', $request->sales_user_id)->where('course_id', $request->course_id)->get();
                    foreach ($leads as $lead) {
                        $lead->sales_user_id = 0;
                    }
                    if ($delete == true) {
                        return response()->json([
                            'message' => 'deleted',
                            'status' => 200
                        ], 200);
                    } else {
                        return response()->json([
                            'message' => 'not deleted',
                            'status' => 500
                        ], 500);
                    }
                } else {
                    return response()->json([
                        'message' => 'not found',
                        'status' => 404
                    ], 404);
                }
            } else {
                return response()->json([
                    'message' => 'not found',
                    'status' => 404
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
        //     } else {
        //         return response()->json([
        //             'message' => 'Unauthenticated',
        //             'status' => 401
        //         ], 401);
        //     }
        // } else {
        //     return response()->json([
        //         'message' => 'Unauthenticated',
        //         'status' => 401
        //     ], 401);
        // }
    }

    public function leadStatusUpdate(Request $request)
    {
        // dd($request->all());
        if (!isset($request->lead_id) || !isset($request->sales_user_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Client id required'
            ], 406);
        }

        $leadId = $request->lead_id;
        $leadStatus = $request->lead_status;

        $leadDetails = LeadDetails::where('lead_id', '=', $leadId)->first();
        if ($leadDetails == "") {
            return response()->json([
                'status' => false,
                'message' => 'Lead details Not found',
                'data' => $request->lead_id
            ], 404);
        }

        if ($leadDetails->sales_user_id == 0) {

            LeadSalesEmployee::create([
                'sales_user_id' => isset($request->sales_user_id) ? $request->sales_user_id : 0,
                'lead_id' => $request->lead_id,
                'assign_by' => isset($request->sales_user_id) ? $request->sales_user_id : 0,
                'active_status' => 1
            ]);

            $leadDetails->sales_user_id = isset($request->sales_user_id) ? $request->sales_user_id : 0;
        }
        $leadDetails->lead_details_status = $leadStatus;
        $leadDetails->save();

        $leadAStatus = LeadStatus::where([
            ['lead_id', '=', $leadId],
            ['lead_status', '=', $leadStatus]
        ])->first();
        // dd(json_encode($leadAStatus));
        $lead_info = LeadDetails::where('lead_id', '=', $leadId)->first();

        $lead_email = $lead_info->student_email;
        $name = $lead_info->full_name;
        $student_id = $lead_info->student_id;
        // dd($lead_email);
        if ($leadAStatus != "" || $leadAStatus != null) {
            if ($leadAStatus->is_active == 0) {

                $leadAStatus->is_active = 1;
                $leadAStatus->save();
                if ($leadAStatus->lead_status == 0 && $leadAStatus->is_active == 1) {
                    if ($lead_info) {
                        $lead_info->lead_details_status = $lead_max_status;
                        $lead_info->save();
                    }
                } else {
                    $lead_max_status = LeadStatus::where('lead_id', $leadId)->where('is_active', '=', 1)->max('lead_status');

                    if ($lead_info) {
                        $lead_info->lead_details_status = $lead_max_status;
                        $lead_info->save();
                    }
                }
            } else if ($leadAStatus->is_active == 1) {
                // if($leadAStatus-)
                $leadAStatus->is_active = 0;
                $leadAStatus->save();

                $lead_max_status = LeadStatus::where('lead_id', $leadId)->where('is_active', '=', 1)->max('lead_status');
                if ($lead_info) {
                    $lead_info->lead_details_status = $lead_max_status;
                    $lead_info->save();
                }
            }
            if ($request->$leadStatus != 0) {
                $college = Http::crm_company()->post('/get-client-name', ['client_id' => $request->client_id]);
                $nameData = json_decode($college->body());
                $college_name = $nameData->data->name;
                Mail::to($lead_email)->queue(new StatusChange($leadStatus, $college_name, $request->course, $name));
            }
        } else {
            $leadAllStatus = LeadStatus::create([
                'lead_status' => $leadStatus,
                'lead_id' => $leadId,
                'to' => $lead_email,
                'name' => $name,
                'student_id' => $student_id,
                'updated_by' => $request->sales_user_id,
                'is_active' => 1
            ])->toArray();

            if ($lead_info) {
                $lead_info->lead_details_status = $leadStatus;
                $lead_info->save();
            }
        }
        $array = [5, 6];


        ///EOF Email Service ///
        $leadAllStatus = LeadStatus::where('lead_id', $leadId)->where('is_active', '=', 1)->get();
        return response()->json([
            'status' => 201,
            'message' => 'Record for this Lead Status',
            'data' => $leadAllStatus
        ], 200);
    }

    public function create_lead_from_form(Request $request)
    {
        $id = round(microtime(true) * 1000);
        $lead_id = intval($id);
        $living_place = [
            "name"
            => "what_state_do_you_live_in?",
            "values" => $request->living_place
        ];
        $course = $request->course;
        $course_code = explode('-', $course);
        $course_code = $course_code[0];
        $existing_lead = LeadDetails::where('lead_id', $lead_id)->first();
        $course_id = CoursesInfo::where('course_code', $course_code)->exists();
        $name = $request->full_name;
        $lead_status = 1;

        // dd($client_logo);
        $logo_details_of_logo = HTTP::crm_company()->get('/documents-details/' . $request->client_id);
        // dd(json_encode($logo_details_of_logo));
        $logo_response_of_logo = json_decode($logo_details_of_logo->body());
        // dd($logo_response_of_logo);
        $client_name = $logo_response_of_logo->client;
        // dd(json_decode($logo_details_of_logo));
        if ($logo_response_of_logo->status !== 404) {
            $logo = $logo_response_of_logo->data->document_name;
            if (!$course_id) {
                $courseId = CoursesInfo::create([
                    'course_code' => $course_code,
                    'course_title' => $course,
                    'course_description' => $course,
                    'status' => 1
                ]);
                if (!$existing_lead) {
                    // dd($request->all());
                    $save = LeadDetails::create([
                        'lead_id' => $lead_id,
                        'student_id' => 0,
                        'full_name' => $request->full_name,
                        'phone_number' => $request->phone_number,
                        'student_email' => $request->student_email,
                        'client_id' => $request->client_id,
                        'campaign_id' => 0,
                        'sales_user_id' => 0,
                        'document_certificate_id' => 0,
                        'course_id' => $courseId->id,
                        'work_location' => $request->work_location,
                        'lead_from' => $request->lead_from,
                        'form_data' => $request->form_data,
                        'star_review' => 0,
                        'lead_apply_date' => Carbon::now(),
                        'lead_details_status' => 1
                    ]);
                    // dd($course);
                    // dd($course->course_title);
                    if ($save) {
                        // HTTP::post('https://crm-mailer.onrender.com/api/send-mail', ['name'=>$name,'lead_status'=>$lead_status,'logo'=>$logo, 'client'=>$client_name,'course'=>$courseId->course_title]);
                        Mail::to($request->student_email)->queue(new NewLeadMail($request->full_name, $logo, $course, $client_name));
                        return response()->json([
                            'message' => 'success',
                            'status' => 201,
                            'data' => $save
                        ], 200);
                    } else {
                        abort(500);
                    }
                } else {
                    return response()->json([
                        'message' => 'exists',
                        'status' => 403
                    ], 403);
                }
            } else {
                // dd($request->all());
                if (!$existing_lead) {
                    $course_id = CoursesInfo::where('course_code', $course_code)->first();
                    $save = LeadDetails::create([
                        'lead_id' => $lead_id,
                        'student_id' => 0,
                        'full_name' => $request->full_name,
                        'phone_number' => $request->phone_number,
                        'student_email' => $request->student_email,
                        'client_id' => $request->client_id,
                        'campaign_id' => 0,
                        'sales_user_id' => 0,
                        'document_certificate_id' => 0,
                        'course_id' => $course_id->id,
                        'work_location' => $request->work_location,
                        'lead_from' => $request->lead_from,
                        'form_data' => $request->form_data,
                        'star_review' => 0,
                        'lead_apply_date' => Carbon::now(),
                        'lead_details_status' => 1
                    ]);
                    // dd($course);
                    if ($save) {
                        Mail::to($request->student_email)->queue(new NewLeadMail($request->full_name, $logo, $course, $client_name));
                        return response()->json([
                            'message' => 'success',
                            'status' => 201,
                            'data' => $save
                        ], 200);
                    } else {
                        abort(500);
                    }
                }
            }
        }
    }
}
