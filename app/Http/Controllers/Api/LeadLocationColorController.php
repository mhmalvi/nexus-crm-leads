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
        $request->validate([
            'location' => 'required|unique:location_color',
            'color' => 'required|unique:location_color',
            'company_id' => 'required'
        ]);
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
        $response = Color::where('company_id', $request->company_id)->orderBy('id', 'desc')->get();
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

    public function deleteColor(Request $request)
    {
        $color = Color::find($request->id);
        if ($color) {
            $response = $color->delete();
            if ($response) {
                return response()->json([
                    'message' => 'Deleted',
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'failed',
                    'status' => 500
                ], 500);
            }
        }
    }

    public function updateColor(Request $request)
    {
        $color = Color::find($request->id);
        $color->location = $request->location;
        $color->color = $request->color;
        $response = $color->save();
        if ($response) {
            return response()->json([
                'message' => 'Updated',
                'status' => 201,
                'data' => $color
            ], 201);
        } else {
            return response()->json([
                'message' => 'failed',
                'status' => 500
            ], 500);
        }
    }
}
