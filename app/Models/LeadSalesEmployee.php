<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadSalesEmployee extends Model
{
    use HasFactory;

    protected $table ='lead_sales_employee';

    protected $fillable=[
        'sales_user_id',
        'lead_id',
        'assign_by'
    ];

    protected $attributes=[
        'active_status'=>1
    ];
}
