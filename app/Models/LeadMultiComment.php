<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LeadDetails;

class LeadMultiComment extends Model
{
    protected $table = "lead_multi_comments";
    use HasFactory;
    protected $guarded = [];
    public function leadDetail()
    {
        return $this->belongsTo(LeadDetails::class);
    }
}
