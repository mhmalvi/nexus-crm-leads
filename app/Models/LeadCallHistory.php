<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadCallHistory extends Model
{
    use HasFactory;

    protected $table = 'lead_call_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lead_id',
        'call_start_time',
        'call_end_time',
        'call_remark',
        'status'
    ];

    /**
     * The attributes Set default value.
     *
     * @var array<int, string>
     */

    protected $attributes =[
        'status'=>1
    ];
}
