<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\LeadSalesEmployee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class SalesController extends Controller
{
    public function sales_list()
    {
        // dd("hello");
        $auth_url = env('AUTH_SERVICE_URL', 'https://crmuser.quadque.digital/api/');
        // dd($auth_url);
        $sales = Http::get($auth_url . 'user/sales-list');
        // dd(json_decode($sales));
        if ($sales) {
            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => json_decode($sales)
            ], 200);
        } else {
            return response()->json([
                'message' => 'failed',
                'status' => 500
            ], 500);
        }
    }

    public function assigned_leads(Request $request)
    {
        $leads = DB::table('lead_details')->join('lead_sales_employee', 'lead_details.lead_id', '=', 'lead_sales_employee.lead_id')->select('lead_details.lead_id', 'lead_details.full_name')->where('lead_details.sales_user_id', $request->sales_user_id)->get();


        if ($leads) {
            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $leads
            ], 200);
        } else {
            return response()->json([
                'message' => 'No leads found',
                'status' => 404
            ], 404);
        }
    }

    public function unassigned_leads(Request $request)
    {
        $leads = DB::table('lead_details')->join('lead_sales_employee', 'lead_details.lead_id', '!=', 'lead_sales_employee.lead_id')->select('lead_details.lead_id', 'lead_details.full_name')->groupBy('lead_details.lead_id')->get();


        if ($leads) {
            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $leads
            ], 200);
        } else {
            return response()->json([
                'message' => 'No leads found',
                'status' => 404
            ], 404);
        }
    }
}
