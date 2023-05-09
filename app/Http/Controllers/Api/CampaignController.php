<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LeadDetails;

class CampaignController extends Controller
{
    //
    /**
     * Create Lead
     * @param Request $request
     * @return
     */
    public function campaignList(Request $request)
    {

        if (!isset($request->client_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Client id required',
                'data' => $request->client_id
            ], 406);
        }

        try {

            $data = DB::table('campaign_details')->where('client_id', '=', $request->client_id)->get();

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

    public function campaign_wise_lead_percentage(Request $request)
    {
        // dd($request->client_id);
        try {
            if ($request->client_id) {
                $total_lead = LeadDetails::select('lead_id')->where('client_id', $request->client_id)->distinct()->count();
                $campaigns = LeadDetails::select('lead_id', 'campaign_id')->distinct('campaign_id')->where('client_id', $request->client_id)->groupBy('campaign_id')->get();
                // dd(json_encode($campaigns));
                // $percentage = ($campaigns->count()/$total_lead)*100;
                // dd(round($percentage,2));
                if ($campaigns) {
                    return response()->json([
                        'message' => 'success',
                        'status' => 200,
                        //   'data'=>round($percentage,2)
                        'data' => $campaigns
                    ]);
                } else {
                    return response()->json([
                        'message' => 'not found',
                        'status' => 404
                    ], 404);
                }
            } else {
                return response()->json([
                    'message' => 'please provide client id',
                    'status' => 404
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message'    => $th->getMessage(),
                'status' => 500,
            ]);
        }
    }
}
