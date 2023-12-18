<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeadCallHistory;
use App\Models\LeadDetails;
use App\Models\Count;

class CountController extends Controller
{
    public function count(){
        $leads = LeadDetails::select('lead_id')->get();
        // dd(json_decode($leads));
        for($i=0;$i<count($leads);$i++){
            $counts = LeadCallHistory::where('lead_id',$leads[$i]->lead_id)->count();
            // dd($counts);
            $row = Count::create([
                'lead_id'=>$leads[$i]->lead_id,
                'call_count'=>$counts
                ]);
                // if($counts){
                //     return response()->json([
                //         'message'=>'succes',
                //         'status'=>200,
                //         'data'=>$counts
                //     ],200);
                // }
        }
        
    }
}
