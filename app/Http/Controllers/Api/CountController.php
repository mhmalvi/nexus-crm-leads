<?php

namespace App\Http\Controllers\Api;

use App\Models\Count;
use App\Models\LeadDetails;
use Illuminate\Http\Request;
use App\Models\LeadCallHistory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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

    public function counts()
    {
        $leads = DB::table('counts')->get();
        // dd(json_decode($leads));
        for ($i = 0; $i < count(json_decode($leads)); $i++) {
            // dd($leads[$i]);
            // $count_detail = json_decode($leads);
            $counts = DB::table('lead_details')->where('lead_id', '=', json_decode($leads[$i]->lead_id))->update(['call_counts' => $leads[$i]->call_count]);
            // dd($counts);
            // $lead_counts = json_decode($counts);
            // dd($lead_counts);
            // $row = Count::create([
            //     'call_count'=>$counts
            // ]);
            // if(json_decode($leads[$i]->call_count)>=0){
            // $counts->call_counts = json_decode($leads[$i]->call_count);
            // $counts->save();
            // if($counts){
            //     return response()->json([
            //         'message'=>'succes',
            //         'status'=>200,
            //         'data'=>$counts
            //     ],200);
            // }
            // }
        }
    }
}
