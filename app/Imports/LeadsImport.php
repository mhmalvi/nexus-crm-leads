<?php

namespace App\Imports;

use App\Models\LeadDetails;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

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

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            LeadDetails::create([
                'full_name' => $row['name'],
            ]);
        }
    }
}
