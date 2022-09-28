<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    //
    /**
     * Create Lead
     * @param Request $request
     * @return
     */
    public function campaignList(Request $request){

        try {

            $data = DB::table('campaign_details')->get();

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
