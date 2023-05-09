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
        try {
            if ($request->client_id) {
                $total_lead = LeadDetails::select('lead_id')->where('client_id', $request->client_id)->distinct()->count();
                $campaigns = LeadDetails::select('campaign_id')->distinct('campaign_id')->where('client_id', $request->client_id)->get();
                foreach ($campaigns as $campaign) {
                    // dd(json_encode($campaign->campaign_id));
                    $campaign_id[] = LeadDetails::select(DB::raw('count(lead_id) as lead'), DB::raw('campaign_id'))->where('campaign_id', $campaign->campaign_id)->get();
                }
                // foreach($campaign_id as $id){
                // $data = collect($campaign_id;
                // }
                // dd(json_encode($campaign_id));
                // for($i=0;$i<count($campaign_id);$i++){
                //     dd(explode(',',$campaign_id[$i][0]));
                // }
                // dd(explode(',',$data));

                dd(json_encode($campaign_id));
                // $percentage = ($campaigns->count()/$total_lead)*100;
                // dd(round($percentage,2));
                if ($campaign_id) {
                    return response()->json([
                        'message' => 'success',
                        'status' => 200,
                        //   'data'=>round($percentage,2)
                        'data' => collect($campaign_id)->flatten(1)->toArray()
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
