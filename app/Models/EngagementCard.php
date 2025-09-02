<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EngagementCard extends Model
{
    //
    protected $fillable = [
        'employee_id',
        'content',
        'start_date',
        'end_date',
        'card_no'
    ];

    // Relationship: EngagementCard belongs to Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
