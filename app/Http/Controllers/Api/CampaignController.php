<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\LeadDetails;
use Illuminate\Support\Facades\Http;

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
        if ($request->bearerToken()) {
            $flag = Http::withToken($request->bearerToken())->post('https://crmuser.queleadscrm.com/api/check-if-token-exists');
            $flag_receive = $flag['data'];
            if ($flag_receive == 1) {
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
                        'status' => 200,
                        'message' => 'All Lead List',
                        'data' => $data
                    ], 200);
                } catch (\Throwable $th) {
                    return response()->json([
                        'status' => false,
                        'message' => $th->getMessage()
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


    public function campaign_wise_lead_percentage(Request $request)
    {
        $data = array();
        try {
            if ($request->client_id) {
                $total_lead = LeadDetails::select('lead_id')->where('client_id', $request->client_id)->distinct()->count();
                $campaigns = LeadDetails::select('campaign_id')->distinct('campaign_id')->where('client_id', $request->client_id)->get();
                foreach ($campaigns as $campaign) {
                    // dd(json_encode($campaign->campaign_id));
                    $lead_id[] = LeadDetails::select(DB::raw('((count(lead_id)*100)/100) as lead'), DB::raw('campaign_id'))->where('campaign_id', $campaign->campaign_id)->get();
                }

                // dd(json_encode($lead_id));
                for ($i = 0; $i < count($lead_id); $i++) {
                    for ($j = 0; $j < count($lead_id[$i]); $j++) {
                        // $lead = );
                        $percentage = ($lead_id[$i][$j]->lead / $total_lead) * 100;

                        // $data.push($percentage);
                        array_push($data, $percentage);
                    }
                }
                // dd($data);

                // dd(json_encode($campaign_id->));
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
