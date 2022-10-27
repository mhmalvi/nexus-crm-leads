<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CampaignDetails;
use App\Models\CoursesInfo;
use App\Models\LeadAmountHistory;
use App\Models\LeadCallHistory;
use App\Models\LeadDetails;
use App\Models\LeadSalesEmployee;
use App\Models\LeadStatus;
use Carbon\Carbon;
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function leadList(Request $request){

        if(!isset($request->client_id)){
            return response()->json([
                'status' => false,
                'message' => 'Client id required'
            ], 406);
        }
        try {

            $data = DB::table('lead_details')
                ->select('lead_details.id as lid', 'lead_details.lead_id  as lead_id', 'lead_details.student_id as student_id', 'lead_details.full_name as full_name', 'lead_details.phone_number as phone_number',
                    'lead_details.student_email as student_email', 'lead_details.client_id as client_id', 'lead_details.campaign_id as campaign_id',
                    'lead_details.sales_user_id as sales_user_id', 'lead_details.document_certificate_id as document_certificate_id',
                    'lead_details.course_id as course_id', 'lead_details.work_location as work_location',
                    'lead_details.lead_from as lead_from', 'lead_details.form_data as form_data',
                    'lead_details.star_review as star_review', 'lead_details.lead_apply_date as lead_apply_date',
                    'lead_details.lead_remarks as lead_remarks', 'lead_details.lead_details_status as lead_details_status',
                    'lead_details.created_at as created_at',
                    'lead_details.updated_at as updated_at',
                    'courses_info.id as cid', 'courses_info.course_code as course_code', 'courses_info.course_title as course_title',
                    'courses_info.course_description as course_description', 'courses_info.status as status')
                ->join('courses_info', function ($join) {
                    $join->on('lead_details.course_id', '=', 'courses_info.id');
                })->where('lead_details.client_id', '=', $request->client_id)->orderBy('lead_details.lead_apply_date', 'desc')
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

    /**
     * Lead Details
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse Details, Lead Status
     */
    public function leadDetails(Request $request){

        if(!isset($request->lead_id)){
            return response()->json([
                'status' => false,
                'message' => 'Lead id required'
            ], 406);
        }

        $leadId = $request->lead_id;

        try {

            //$leadDetails = LeadDetails::where('lead_id', '=', $leadId)->first();

            $leadDetails = DB::table('lead_details')
                ->join('courses_info', function ($join) {
                    $join->on('lead_details.course_id', '=', 'courses_info.id');
                })->where('lead_details.lead_id', '=', $leadId)
                ->get();
            //dd($leadDetails);

            if(count($leadDetails)==0){
                //dd($leadDetails);
                return response()->json([
                    'status' => false,
                    'message' => 'Lead details Not found'
                ], 404);
            }

            $leadAllStatus = LeadStatus::where('lead_id', '=', $leadId)->get();
            $isData = false;
            if($leadAllStatus!="") {

                foreach ($leadAllStatus as $leadAStatus) {
                    if ($leadAStatus['lead_status'] == '1') {
                        $isData = true;
                        break;
                    }
                }
            }
              if($isData){
                  $leadAllStatus = $leadAllStatus->toArray();
              }else{

                  $leadAllStatus = new LeadStatus;
                  $leadAllStatus->lead_status =1;
                  $leadAllStatus->lead_id =$leadId;
                  $leadAllStatus->created_at =$leadDetails[0]->lead_apply_date;
                  $leadAllStatus->save();
                  $leadAllStatus = $leadAllStatus->toArray();

//                  $leadAllStatus = LeadStatus::create([
//                      'lead_status' => 1,
//                      'lead_id' => $leadId,
//                      'created_at' =>$leadDetails->lead_apply_date
//
//                  ])->get()->toArray();

                }

            //dd($leadAllStatus->toArray());
//            }else{
//                return response()->json([
//                    'status' => false,
//                    'message' => 'Lead details Not found',
//                    'data'=>$request->lead_id
//                ], 404);
//
//            }

            $leadDetails[0]->form_data = json_decode($leadDetails[0]->form_data);

            $leadAmountHistory = LeadAmountHistory::where('lead_id','=',$request->lead_id)->get()->toArray();
            $leadCallHistory = LeadCallHistory::where('lead_id','=',$request->lead_id)->get()->toArray();
            $leadSalesEmployeeHistory = LeadSalesEmployee::where('lead_id','=',$request->lead_id)->get()->toArray();
            return response()->json([
                'status' => true,
                'message' => 'All Lead List',
                'leadDetails' => $leadDetails[0],
                'leadAllStatus' => $leadAllStatus,
                'leadCallHistory' => $leadCallHistory,
                'leadAmountHistory' => $leadAmountHistory,
                'leadSalesEmployeeHistory' => $leadSalesEmployeeHistory

            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }

    /**
     * Lead Status Update
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse Status
     */
    public function leadStatusUpdate(Request $request){

        if(!isset($request->lead_id) || !isset($request->sales_user_id) ){
            return response()->json([
                'status' => false,
                'message' => 'Client id required'
            ], 406);
        }

        $leadId = $request->lead_id;
        $leadStatus = $request->lead_status;

        //dd($leadAStatus);

        try {

            $leadDetails = LeadDetails::where('lead_id','=', $leadId)->first();
            if($leadDetails == ""){
                return response()->json([
                    'status' => false,
                    'message' => 'Lead details Not found',
                    'data'=>$request->lead_id
                ], 404);

            }

            if($leadDetails->sales_user_id == 0){

                LeadSalesEmployee::create([
                    'sales_user_id'=>isset($request->sales_user_id)?$request->sales_user_id:0,
                    'lead_id'=>$request->lead_id,
                    'assign_by'=>isset($request->sales_user_id)?$request->sales_user_id:0,
                    'active_status'=>1
                ]);

                $leadDetails->sales_user_id = isset($request->sales_user_id)?$request->sales_user_id:0;
            }
            $leadDetails->lead_details_status = $leadStatus;
            $leadDetails->save();

            $leadAStatus = LeadStatus::where([
                ['lead_id', '=', $leadId],
                ['lead_status', '=', $leadStatus]])
                ->first();
           // dd($leadAStatus);
            if($leadAStatus!=""){
                $leadAllStatus = $leadAStatus->toArray();
            }else{
                $leadAllStatus = LeadStatus::create([
                    'lead_status' => $leadStatus,
                    'lead_id' => $leadId,
                    'updated_by' => $request->sales_user_id,
                   // 'created_at' =>Carbon::now()
                ])->toArray();
            }

            return response()->json([
                'status' => true,
                'message' => 'Record for this Lead Status',
                'leadAllStatus' => $leadAllStatus

            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }

    /**
     * Lead Update
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function leadUpdate(Request $request){

        if(!isset($request->lead_id) && !isset($request->student_id) ){
            return response()->json([
                'status' => false,
                'message' => 'Lead id and Student Id required '
            ], 406);
        }


        try {

            $leadDetails = LeadDetails::where('lead_id','=', $request->lead_id)->first();
            if($leadDetails == ""){
                return response()->json([
                    'status' => false,
                    'message' => 'Lead details Not found',
                    'data'=>$request->lead_id
                ], 404);

            }

            if(isset($request->student_id))
                $leadDetails->student_id = $request->student_id;
            $leadDetails->save();

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

    /**
     * Lead Quality Update
     * @param Request $request
     * @return Lead
     */
    public function leadQualityUpdate(Request $request){

        if(!isset($request->lead_id) && !isset($request->sales_user_id) ){
            return response()->json([
                'status' => false,
                'message' => 'Lead id and Sales Id required '
            ], 406);
        }


        try {

            $leadDetails = LeadDetails::where('lead_id','=', $request->lead_id)->first();
            if($leadDetails == ""){
                return response()->json([
                    'status' => false,
                    'message' => 'Lead details Not found',
                    'data'=>$request->lead_id
                ], 404);

            }

            if($leadDetails->sales_user_id>0){
                if(isset($request->star_review))
                    $leadDetails->star_review = (int)$request->star_review;
                if(isset($request->lead_remarks))
                    $leadDetails->lead_remarks = $request->lead_remarks;
                $leadDetails->save();
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Lead Not Assign yet ',
                    'data'=>$request->lead_id
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

    /**
     * Lead Assign to Sales Employee
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse Assign history
     */
    public function leadAssign(Request $request){

        if(!isset($request->lead_id) || !isset($request->sales_user_id) ){
            return response()->json([
                'status' => false,
                'message' => 'Lead id and Sales Id required'
            ], 406);
        }

        try {

            LeadSalesEmployee::updateOrcreate([
                'sales_user_id' => $request->sales_user_id,
                'lead_id' => $request->lead_id,
                'assign_by'=>$request->assign_by
            ])->toArray();
            $leadSalesEmployeeHistory = LeadSalesEmployee::where('lead_id','=',$request->lead_id)->get()->toArray();
            return response()->json([
                'status' => true,
                'message' => 'Lead Sales Employee added successfully',
                'data'   =>$leadSalesEmployeeHistory
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }

    /**
     * Lead Amount History
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse History
     */
    public function leadAddAmount(Request $request){

        if(!isset($request->lead_id) || !isset($request->amount)){
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
            $leadAmountHistory = LeadAmountHistory::where('lead_id','=',$request->lead_id)->get()->toArray();
            return response()->json([
                'status' => true,
                'message' => 'Lead amount added successfully',
                'data'   =>$leadAmountHistory

            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Lead Call History
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse Call History
     */
    public function leadAddCallHistory(Request $request){

        if(!isset($request->lead_id) || !isset($request->call_start_time) || !isset($request->call_end_time)){
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

            $leadCallHistory = LeadCallHistory::where('lead_id','=',$request->lead_id)->get()->toArray();

            return response()->json([
                'status' => true,
                'message' => 'Lead Call history added successfully',
                'data'   => $leadCallHistory
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


}
