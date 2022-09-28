<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CampaignDetails;
use App\Models\CoursesInfo;
use App\Models\LeadDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    /**
     * Create Lead
     * @param Request $request
     * @return
     */
    public function createLead(Request $request)
    {

        if(!isset($request->client_id)){
            return response()->json([
                'status' => false,
                'message' => 'Client id required',
                'data'=>$request->client_id
            ], 406);
        }

        try {

            $leadsDataArray =  $request->data;

//            return response()->json([
//                'status' => false,
//                'message' => '',
//                'data'=>$leadsDataArray
//            ], 200);

//
//            $campign = CampaignDetails::create([
//                'campaign_name' => $request->campaign_name,
//                'campaign_id' => $request->campaign_id,
//                'client_id' => $request->client_id,
//                'business_id' => $request->business_id,
//                'business_name' => $request->business_name,
//                'start_time' => $request->start_time,
//                'stop_time' => $request->start_time,
//                'campaign_status' => $request->campaign_status
//            ]);
//
//            $courses = CoursesInfo::create([
//                'course_code' => $request->campaign_name,
//                'course_title' => $request->start_time,
//                'course_description' => $request->start_time,
//                'status' => $request->campaign_status
//            ]);
//
//            $leadDetails = LeadDetails::create([
//                'lead_id' => $request->lead_id ,
//                'student_id' => isset($request->student_id)?$request->student_id:'',
//                'full_name' => isset($request->full_name)?$request->full_name:'',
//                'phone_number' => isset($request->phone_number)?$request->phone_number:'',
//                'student_email' => isset($request->student_email)?$request->student_email:'',
//                'client_id' => isset($client_id)?$client_id:'' ,
//                'campaign_id' => isset($campign->id)?$campign->id:'',
//                'sales_user_id' => isset($request->sales_user_id)?$request->sales_user_id:'',
//                'document_certificate_id' => isset($request->document_certificate_id)?$request->document_certificate_id:'',
//                'course_id' => isset($courses->id)?$courses->id:'',
//                'work_location' => isset($request->work_location)?$request->work_location:'',
//                'lead_from' => isset($request->lead_from)?$request->lead_from:'',
//                'star_review' => isset($request->star_review)?$request->star_review:'',
//                'lead_apply_date' => isset($request->lead_apply_date)?$request->lead_apply_date:'',
//                'lead_remarks' => isset($request->lead_remarks)?$request->lead_remarks:''
//            ]);

            return response()->json([
                'status' => true,
                'message' => 'Lead Created Successfully',
               // 'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 201);

//            return response()->json([
//                'status' => true,
//                'message' => 'User Created Successfully',
//                // 'token' => $user->createToken("API TOKEN")->plainTextToken
//                'data'=>$request->data
//            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Create Lead
     * @param Request $request
     * @return
     */
    public function leadList(Request $request){

        try {

            $data = DB::table('lead_details')
                ->join('courses_info', function ($join) {
                    $join->on('lead_details.course_id', '=', 'courses_info.id');
                })
                ->get();

            //dd($data);

            return response()->json([
                'status' => true,
                'message' => 'All Lead List',
                'data' => $data
            ], 200);


        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }
}
