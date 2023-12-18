<?php

namespace App\Imports;

use App\Models\LeadDetails;
use App\Models\LeadStatus;
use App\Models\CoursesInfo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class LeadsImport implements ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private $client_id;
    public $flag;
    public function  __construct($client_id)
    {
        $this->client_id = $client_id;
    }

    public function collection(Collection $row)
    {
        try {
            for ($i = 1; $i < count($row); $i++) {
                $id = round(microtime(true) * 1000);
                $lead_id = intval($id);
                $course_code = explode('-', $row[$i][12]);
                $course_id = CoursesInfo::where('course_code', $course_code[0])->exists();
                if (!$course_id) {
                    $courseId = CoursesInfo::create([
                        'course_code' => $course_code[0],
                        'course_title' => $row[$i][12],
                        'course_description' => $row[$i][12],
                        'status' => 1
                    ]);
                    $existing_lead = LeadDetails::where('lead_id', $lead_id)->orWhere('student_email', $row[$i][2])->first();
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
                            "lead_apply_date" => Date::excelToDateTimeObject($row[$i][0])->format('Y-m-d'),
                            "lead_remarks" => $row[$i][8],
                            "lead_details_status" => 1,
                            'course_id' => $courseId->id
                        ]);

                        $status = LeadStatus::create([
                            'lead_status' => 1,
                            'is_active' => 1,
                            'lead_id' => $lead_id
                        ]);
                        if ($excel) {
                            $flag = 1;
                        } else {
                            $flag = 0;
                        }
                    } else {
                        $flag = 3;
                    }
                } else {
                    $course_id = CoursesInfo::where('course_code', $course_code[0])->first();
                    $existing_lead = LeadDetails::where('lead_id', $lead_id)->orWhere('student_email', $row[$i][2])->first();
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
                            "lead_apply_date" => Date::excelToDateTimeObject($row[$i][0])->format('Y-m-d'),
                            "lead_remarks" => $row[$i][8],
                            "lead_details_status" => 1,
                            'course_id' => $course_id->id
                        ]);
                        $status = LeadStatus::create([
                            'lead_status' => 1,
                            'is_active' => 1,
                            'lead_id' => $lead_id
                        ]);
                        if ($excel) {
                            $flag = 1;
                        } else {
                            $flag = 0;
                        }
                    } else {
                        $flag = 3;
                    }
                }
            }
            $this->flag = $flag;
        } catch (\Throwable $th) {
            $flag = 0;
            $this->flag = $flag;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
