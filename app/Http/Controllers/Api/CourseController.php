<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoursesInfo;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function editCourse($id)
    {
        $course = CoursesInfo::find($id);
        if ($course) {
            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $course
            ], 200);
        } else {
            return response()->json([
                'message' => 'no course found',
                'status' => 404
            ], 404);
        }
    }
}
