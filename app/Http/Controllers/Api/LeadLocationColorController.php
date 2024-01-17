<?php

namespace App\Http\Controllers\Api;

use App\Models\Color;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class LeadLocationColorController extends Controller
{
    public function add_color(Request $request)
    {

        $response = Color::create([
            'location' => $request->location,
            'color' => $request->color,
            'company_id' => $request->company_id
        ]);

        if ($response) {
            return response()->json([
                'message' => 'Color saved successfully',
                'status' => 201,
                'data' => $response
            ], 201);
        } else {
            return response()->json([
                'message' => 'Failed',
                'status' => 500
            ], 500);
        }
    }

    public function getColor(Request $request)
    {
        $response = Color::orderBy('id')->get();
        if ($response) {
            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $response
            ], 200);
        } else {
            return response()->json([
                'message' => 'No color found'
            ], 404);
        }
    }
}
