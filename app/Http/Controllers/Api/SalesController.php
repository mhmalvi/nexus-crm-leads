<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeadSalesEmployee;
use App\Models\LeadDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SalesController extends Controller
{
    public function sales_list(Request $request, $id)
    {
        // $flag = Http::timeout(-1)->withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
        // $flag_receive = $flag['data'];
        // if ($flag_receive == 1) {
            $auth_url = env('COMPANY_SERVICE_URL', env('COMPANY_SERVICE_API', '') . '/');
            $sales_from_company_service = [];
            $sales = DB::connection('company')->table('company_sales_employee')->where('company_id', $id)->where('active', 1)->get();
            // $sales = Http::get($auth_url . 'company/sales/' . $id);
            // $sales_name = Http::get('https://crmuser.queleadscrm.com/api/user/sales-list');
            $sales_name = DB::connection('user')->table('users')
                ->join('user_profile', 'users.id', '=', 'user_profile.user_id')
                ->select('users.*', 'user_profile.*')
                ->where('users.role_id', '=', 5)
                ->where('suspend', 0)
                ->get();
            for ($i = 0; $i < count($sales); $i++) {
                for ($j = 0; $j < count($sales_name); $j++) {
                    if ($sales[$i]->user_id == $sales_name[$j]->user_id) {
                        $sales_names[] = $sales_name[$j];
                    }
                }
            }

            if ($sales) {
                return response()->json([
                    'message' => 'success',
                    'status' => 200,
                    'data' => $sales_names
                ], 200);
            } else {
                return response()->json([
                    'message' => 'failed',
                    'status' => 500
                ], 500);
            }
        // } else {
        //     return response()->json([
        //         'message' => 'unauthenticated',
        //         'status' => 401
        //     ], 401);
        // }
    }

    public function assigned_leads(Request $request, $id)
    {
        // if ($request->bearerToken()) {
        //     $userApi = env('USER_SERVICE_API', '');
        //     $flag = Http::withToken($request->bearerToken())->post($userApi . '/check-if-token-exists');
        //     $flag_receive = $flag['data'];
        //     if ($flag_receive == 1) {
                $leads = DB::table('lead_details')->join('courses_info', 'lead_details.course_id', '=', 'courses_info.id')->select('lead_details.lead_id as lead_id', 'lead_details.full_name as full_name', 'courses_info.course_title as course', 'lead_details.sales_user_id as sales_user_id', 'lead_details.call_count as call_count')->where('lead_details.sales_user_id', $id)->get()->toArray();

                for ($j = 0; $j < count($leads); $j++) {
                    $last_count = DB::table('lead_call_history')->where('lead_id', $leads[$j]->lead_id)->max('call_start_time');
                    $leads[$j]->last_call = $last_count;
                }
                if ($leads) {
                    return response()->json([
                        'message' => 'success',
                        'status' => 200,
                        'data' => $leads
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'No leads found',
                        'status' => 404
                    ], 404);
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

    public function unassigned_leads(Request $request, $id)
    {
        // $userApi = env('USER_SERVICE_API', '');
        // $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
        // $flag_receive = $flag['data'];
        // if ($flag_receive == 1) {
        $leads = DB::table('lead_details')->join('courses_info', 'lead_details.course_id', '=', 'courses_info.id')->select('lead_details.lead_id', 'lead_details.full_name', 'courses_info.course_title as course', 'lead_details.call_count as call_count')->orderBy('lead_details.id', 'desc')->get()->toArray();
        for ($j = 0; $j < count($leads); $j++) {
            $last_count = DB::table('lead_call_history')->select('call_start_time')->where('lead_id', $leads[$j]->lead_id)->max('call_start_time');
            $leads[$j]->last_call = $last_count;
        }
        if ($leads) {
            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $leads
            ], 200);
        } else {
            return response()->json([
                'message' => 'No leads found',
                'status' => 404
            ], 404);
        }
        // } else {
        //     return response()->json([
        //         'message' => 'unauthenticated',
        //         'status' => 401
        //     ], 401);
        // }
    }

    public function assign_leads_to_sales(Request $request)
    {
        // $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
        // $flag_receive = $flag['data'];
        // if ($flag_receive == 1) {
            $lead_exist = LeadDetails::where('lead_id', $request->lead_id)->where('sales_user_id', $request->sales_user_id)->exists();
            if ($lead_exist) {
                return response()->json([
                    'message' => 'Lead is already assigned',
                    'status' => 403,
                ]);
            } else {
                $lead = LeadSalesEmployee::create([
                    'sales_user_id' => $request->sales_user_id,
                    'lead_id' => $request->lead_id,
                    'active_status' => 1,
                    'assign_by' => $request->assign_by
                ]);

                $lead_exist = LeadDetails::where('lead_id', $request->lead_id)->first();
                $lead_exist->sales_user_id = $request->sales_user_id;
                $response = $lead_exist->save();

                if ($lead && $response) {
                    return response()->json([
                        'message' => "Lead assigned successfully",
                        'status' => 201,
                        'data' => $lead
                    ], 201);
                } else {
                    return response()->json([
                        'message' => "Failed to assign lead",
                        'status' => 500,
                        'data' => $lead
                    ], 500);
                }
            }
        // } else {
        //     return response()->json([
        //         'message' => 'unauthenticated',
        //         'status' => 401
        //     ], 401);
        // }
    }

    public function unassign_leads(Request $request)
    {
        // $userApi = env('USER_SERVICE_API', '');
        // $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
        // $flag_receive = $flag['data'];
        // if ($flag_receive == 1) {
            if (!$request->lead_id || !$request->sales_user_id) {
                return response()->json([
                    'message' => 'Lead id or sales id missing',
                    'status' => 500
                ], 500);
            } else {
                // dd($request->lead_id,$request->sales_user_id);
                //   $data = LeadSalesEmployee::where('lead_id',$request->lead_id)->where('sales_user_id',$request->sales_user_id)->first();
                //     $flag = $data->delete();

                $remove_sales_user_id_from_lead_details_table = LeadDetails::where('lead_id', $request->lead_id)->first();
                $remove_sales_user_id_from_lead_details_table->sales_user_id = 0;
                $response = $remove_sales_user_id_from_lead_details_table->save();
                if ($response) {
                    return response()->json([
                        'message'    => 'Unassigned successfully',
                        'status' => 201
                    ]);
                } else {
                    return response()->json([
                        'message' => 'failed',
                        'status' => 500
                    ], 500);
                }
            }
        // } else {
        //     return response()->json([
        //         'message' => 'failed',
        //         'status' => 401
        //     ], 401);
        // }
    }

    public function lead_list_in_sales(Request $request, $sales_id, $company_id)
    {
        // if ($request->bearerToken()) {
        //     $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
        //     $flag_receive = $flag['data'];
        //     if ($flag_receive == 1) {
                $lead_list = LeadDetails::join('courses_info', 'lead_details.course_id', '=', 'courses_info.id')->where('client_id', $company_id)->where('sales_user_id', $sales_id)->get();
                if ($lead_list) {
                    return response()->json([
                        'message'    => 'success',
                        'status' => 200,
                        'data' => $lead_list
                    ], 200);
                } else {
                    return response()->json([
                        'message'    => 'Not found',
                        'status' => 404
                    ], 404);
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
}
