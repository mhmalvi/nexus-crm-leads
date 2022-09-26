<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursesInfo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_code',
        'course_title',
        'course_description',
        'status',
        'sales_user_id',
        'document_certificate_id',
        'course_id',
        'work_location',
        'lead_from',
        'star_review',
        'lead_apply_date',
        'lead_remarks',
        'lead_remarks'
    ];
}
