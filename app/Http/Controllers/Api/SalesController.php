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

    public function assigned_leads($id)
    {
        $leads = DB::table('lead_details')->join('lead_details', 'lead_details.lead_id', '=', 'lead_sales_employee.lead_id')->select('lead_sales_employee.lead_id', 'lead_details.full_name')->where('lead_details.sales_user_id', $id)->get();
        // $leads  = DB::table('lead_sales_employee')->select('lead_id', 'full_name')->where('lead_details.sales_user_id', $id)->get();

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

    public function unassigned_leads()
    {
        $leads = DB::table('lead_details')->join('lead_sales_employee', 'lead_details.lead_id', '!=', 'lead_sales_employee.lead_id')->select('lead_details.lead_id', 'lead_details.full_name')->get();


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

    public function assign_leads_to_sales(Request $request)
    {
        $lead_exist = LeadSalesEmployee::where('lead_id', $request->lead_id)->exists();
        if ($lead_exist) {
            return response()->json([
                'message' => 'Lead is already assigned',
                'status' => 403,
            ]);
        } else {
            $lead = LeadSalesEmployee::create([
                'sales_user_id' => $request->sales_user_id,
                'lead_id' => $request->lead_id,
                'active_status' => 1,
                'assign_by' => $request->sales_user_id
            ]);
            if ($lead) {
                return response()->json([
                    'message' => "Lead assigned successfully",
                    'status' => 201,
                    'data' => $lead
                ], 201);
            } else {
                return response()->json([
                    'message' => "Failed to assign lead",
                    'status' => 500,
                    'data' => $lead
                ], 500);
            }
        }
    }
}
