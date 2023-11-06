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
        $leads = LeadDetails::all();
        for($i=0;$i<count($leads);$i++){
            
        }
    }
}
