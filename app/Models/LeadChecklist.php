<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadChecklist extends Model
{
    use HasFactory;

    protected $table = 'lead_checklist';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'user_id',
        'course_id',
        'title'
    ];

    protected $attributes = [
        'status' => 1
    ];

}
