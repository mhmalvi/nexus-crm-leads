<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CampaignDetails;
use App\Models\LeadDetails;
use Illuminate\Http\Request;
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
        try {
            //Validated
            $validateUser = Validator::make($request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|unique:lead_id'
                ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $campign = CampaignDetails::create([
                'campaign_name ' => $request->campaign_name,
                'start_time' => $request->start_time,
                'stop_time' => $request->start_time,
                'campaign_status' => $request->campaign_status
            ]);

            $courses = CampaignDetails::create([
                'campaign_name ' => $request->campaign_name,
                'start_time' => $request->start_time,
                'stop_time' => $request->start_time,
                'campaign_status' => $request->campaign_status
            ]);


            $leadDetails = LeadDetails::create([
                'lead_id ' => $request->lead_id ,
                'campaign_id' => $campign->id,
                'sales_user_id' => isset($request->sales_user_id)?$request->sales_user_id:'',
                'document_certificate_id' => isset($request->document_certificate_id)?$request->document_certificate_id:'',
                'course_id' => isset($request->course_id)?$request->course_id:'',
                'work_location' => isset($request->work_location)?$request->work_location:'',
                'lead_from' => isset($request->lead_from)?$request->lead_from:'',
                'star_review' => isset($request->star_review)?$request->star_review:'',
                'lead_apply_date' => isset($request->lead_apply_date)?$request->lead_apply_date:'',
                'lead_remarks' => isset($request->lead_remarks)?$request->lead_remarks:''
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
