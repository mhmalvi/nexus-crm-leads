<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadStudentDocuments extends Model
{
    use HasFactory;

    protected $table = 'lead_student_documents';

    protected $fillable=[
        'checklist_id',
        'lead_id',
        'document_id',
        'student_id'
    ];

    protected $attributes=[
      'status'=>1
    ];
}
