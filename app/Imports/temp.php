<?php

namespace App\Imports;

use App\Models\LeadDetails;
use App\Models\CoursesInfo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LeadsImport implements ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private $client_id;
    public function  __construct($client_id)
    {
        $this->client_id = $client_id;
    }



    public function collection(Collection $row)
    {
        // foreach ($row as $rows) {
        //     $course_code = explode('-', $rows[12]);
        // }
        // dd($this->client_id);
        for ($i = 1; $i < count($row); $i++) {
            $id = round(microtime(true) * 1000);
            $lead_id = intval($id);
            $course_code = explode('-', $row[$i][12]);
            // dd($course_code[0]);
            // dd($course_code);
            // $array = [
            //     'lead_id' => $lead_id,
            //     'full_name' => $row[$i][1],
            //     "student_email" => $row[$i][2],
            //     'lead_from' => 'file upload',
            //     'star_review' => 0,
            //     'document_certificate_id' => 0,
            //     'student_id' => 0,
            //     "work_location" => $row[$i][7],
            //     "phone_number" => $row[$i][3],
            //     "lead_apply_date" => $row[$i][0],
            //     "lead_remarks" => $row[$i][8],
            // ];
            // dd($array);
            $course_id = CoursesInfo::where('course_code', $course_code[0])->exists();
            if (!$course_id) {
                $courseId = CoursesInfo::create([
                    'course_code' => $course_code[0],
                    'course_title' => $row[$i][12],
                    'course_description' => $row[$i][12],
                    'status' => 1
                ]);
                $existing_lead = LeadDetails::where('lead_id', $lead_id)->first();
                if (!$existing_lead) {
                    $excel = LeadDetails::create([
                        'lead_id' => $lead_id,
                        'full_name' => $row[$i][1],
                        "student_email" => $row[$i][2],
                        'lead_from' => 'file upload',
                        'star_review' => 0,
                        'document_certificate_id' => 0,
                        'student_id' => 0,
                        "work_location" => $row[$i][7],
                        "client_id" => $this->client_id,
                        "phone_number" => $row[$i][3],
                        "lead_apply_date" => $row[$i][0],
                        "lead_remarks" => $row[$i][8],
                        "lead_details_status" => 1,
                        'course_id' => $courseId->id,
                    ]);
                }
                if ($excel) {
                    return response()->json([
                        'message' => 'success',
                        'status' => 201
                    ]);
                } else {
                    return response()->json([
                        'message' => 'please rearrange excel sheet columns',
                        'status' => 400
                    ]);
                }
            } else {
                $existing_lead = LeadDetails::where('lead_id', $lead_id)->first();
                $courseId = CoursesInfo::where('course_code', $course_code[0])->first();
                if (!$existing_lead) {
                    $excel = LeadDetails::create([
                        'lead_id' => $lead_id,
                        'full_name' => $row[$i][1],
                        "student_email" => $row[$i][2],
                        'lead_from' => 'file upload',
                        'star_review' => 0,
                        'document_certificate_id' => 0,
                        'student_id' => 0,
                        "work_location" => $row[$i][7],
                        "client_id" => $this->client_id,
                        "phone_number" => $row[$i][3],
                        "lead_apply_date" => $row[$i][0],
                        "lead_remarks" => $row[$i][8],
                        "lead_details_status" => 1,
                        'course_id' => $courseId->id,
                    ]);
                }
                if ($excel) {
                    return response()->json([
                        'message' => 'success',
                        'status' => 201
                    ]);
                } else {
                    return response()->json([
                        'message' => 'please rearrange excel sheet columns',
                        'status' => 400
                    ]);
                }
            }
            // dd($courseId->id);


        }
    }

    // public function headingRow(): int
    // {
    //     return 2;
    // }

    // public function startRow(): int
    // {
    //     return 2;
    // }


}
