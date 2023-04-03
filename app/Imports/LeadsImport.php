<?php

namespace App\Imports;

use App\Models\LeadDetails;
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
    // public function model(array $rows)
    // {
    //     // dd($rows);
    //     foreach ($rows as $row) {
    //         // for ($i = 1; $i < count($row); $i++) {
    //         $id = round(microtime(true) * 1000);
    //         $lead_id = intval($id);
    //         $existing_lead = LeadDetails::where('lead_id', $lead_id)->first();
    //         if (!$existing_lead) {
    //             // dd("hel");
    //             return new LeadDetails([
    //                 // "lead_id" => 67768658775,
    //                 "full_name" => $row['name'] ?? '',
    //                 "student_email" => $row['email'] ?? '',
    //                 "phone_number" => $row['contact'] ?? '',
    //                 "work_location" => $row['work_location'] ?? '',
    //                 "phone_number" => $row['contact'] ?? '',
    //                 "lead_apply_date" => $row['date'] ?? '',
    //                 "lead_remarks" => $row['remark'] ?? '',
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'message' => 'Already exists',
    //                 'status' => 403,
    //                 'lead_id' => $existing_lead
    //             ]);
    //         }
    //     }
    // }



    public function collection(Collection $row)
    {
        // foreach ($rows as $row) {
        for ($i = 1; $i < count($row); $i++) {
            $id = round(microtime(true) * 1000);
            $lead_id = intval($id);
            $array = [
                'lead_id' => $lead_id,
                'full_name' => $row[$i][1],
                "student_email" => $row[$i][2],
                'lead_from' => 'file upload',
                'star_review' => 0,
                'document_certificate_id' => 0,
                'student_id' => 0,
                "work_location" => $row[$i][7],
                "phone_number" => $row[$i][3],
                "lead_apply_date" => $row[$i][0],
                "lead_remarks" => $row[$i][8],
            ];
            // dd($array);
            $existing_lead = LeadDetails::where('lead_id', $lead_id)->first();
            if (!$existing_lead) {
                LeadDetails::create([
                    'lead_id' => $lead_id,
                    'full_name' => $row[$i][1],
                    "student_email" => $row[$i][2],
                    'lead_from' => 'file upload',
                    'star_review' => 0,
                    'document_certificate_id' => 0,
                    'student_id' => 0,
                    
                    "work_location" => $row[$i][7],
                    'form_data' => 5,
                    "phone_number" => $row[$i][3],
                    "lead_apply_date" => $row[$i][0],
                    "lead_remarks" => $row[$i][8],
                ]);
            }
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
