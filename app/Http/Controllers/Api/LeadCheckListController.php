<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeadCallHistory;
use App\Models\LeadChecklist;
use App\Models\LeadStudentDocuments;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeadCheckListController extends Controller
{
    /**
     * List Lead CheckList
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if(!isset($request->course_id))
            return response()->json([
                'status' => false,
                'message' => 'Course Id not found',
            ], 401);

        //dd($request->id);
        try {
            $leadCheckList = LeadChecklist::select('*');

            $leadCheckList =$leadCheckList->where('course_id',$request->course_id);
            if(isset($request->client_id))
                $leadCheckList =$leadCheckList->where('client_id',$request->client_id);
            if(isset($request->lead_id))
                $leadCheckList =$leadCheckList->where('lead_id',$request->lead_id);
            $leadCheckList =$leadCheckList->where('status',1);
            $leadCheckList = $leadCheckList->get();
           // dd($leadCheckList);
            if($leadCheckList==""){
                return response()->json([
                    'status' => false,
                    'message' => 'Lead Checklist Data not found',
                ], 401);
            }

            return response()->json([
                'status' => true,
                'message' => 'All Lead Checklist',
                'data'    => $leadCheckList->toArray()
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Create Lead CheckList
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse Payment Setting
     */
    public function create(Request $request)
    {
        if(!isset($request->user_id) || !isset($request->client_id) || !isset($request->course_id))
            return response()->json([
                'status' => false,
                'message' => 'User Id, Client Id and Course id required',
            ], 401);

        try {

            $data = LeadChecklist::updateOrcreate([
                'client_id' => $request->client_id,
                'user_id' => $request->user_id,
                'course_id' => $request->course_id,
                'title' => isset($request->title)?$request->title:''
            ])->toArray();

            return response()->json([
                'status' => true,
                'message' => 'Payment Setting Created Successfully',
                'data'  => $data
            ], 201);

        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update Lead CheckList
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        if(!isset($request->id))
            return response()->json([
                'status' => false,
                'message' => 'Id not found',
            ], 401);

        try {
            $leadCheckList = LeadChecklist::find($request->id);
            if($leadCheckList==""){
                return response()->json([
                    'status' => false,
                    'message' => 'Lead Checklist Data not found',
                ], 401);
            }

            if(isset($request->course_id))
                $leadCheckList->course_id = $request->course_id;
            if(isset($request->title))
                $leadCheckList->title = $request->title;
            $leadCheckList->save();
            return response()->json([
                'status' => true,
                'message' => 'Lead Checklist Update Successfully',
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Soft Delete Lead CheckList
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        if(!isset($request->id))
            return response()->json([
                'status' => false,
                'message' => 'Id not found',
            ], 401);

        //dd($request->id);
        try {
            $leadCheckList = LeadChecklist::find($request->id);
            if($leadCheckList==""){
                return response()->json([
                    'status' => false,
                    'message' => 'Lead Checklist Data not found',
                ], 401);
            }

            $leadCheckList->status = 0;
            $leadCheckList->save();
            return response()->json([
                'status' => true,
                'message' => 'Lead Checklist Delete Successfully',
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Add Documents
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse Payment Setting
     */
    public function addStudentDocuments(Request $request)
    {
        if(!isset($request->checklist_id) || !isset($request->lead_id) || !isset($request->document_id) || !isset($request->student_id))
            return response()->json([
                'status' => false,
                'message' => 'Checklist Id Id, Lead Id, Document Id and Student id required',
            ], 401);

        try {

            $data = LeadStudentDocuments::updateOrcreate([
                'checklist_id' => $request->checklist_id,
                'lead_id' => $request->lead_id,
                'document_id' => $request->document_id,
                'student_id' => isset($request->student_id)?$request->student_id:''
            ])->toArray();

            return response()->json([
                'status' => true,
                'message' => 'Student documents Added Successfully',
                'data'  => $data
            ], 201);

        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Get Student documents
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse Call History
     */
    public function getStudentDocuments(Request $request){

        if(!isset($request->lead_id) || !isset($request->checklist) || !isset($request->student_id)){
            return response()->json([
                'status' => false,
                'message' => 'Lead id , student id and Checklist required'
            ], 406);
        }
        $checklistIds = json_decode($request->checklist);

        try {

            $data = LeadStudentDocuments::where('lead_id','=',$request->lead_id)->where('student_id','=',$request->student_id)->whereIn('checklist_id', $checklistIds)->get()->toArray();

            return response()->json([
                'status' => true,
                'message' => 'Students List',
                'data'   => $data
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

}
