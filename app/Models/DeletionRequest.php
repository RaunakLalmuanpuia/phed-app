<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeletionRequest extends Model
{
    //
    protected $fillable = [
        'employee_id',
        'reason',
        'request_date',
        'approval_status',
        'approval_date',
    ];

    protected $casts = [
        'request_date' => 'date',
        'approval_date' => 'date',
    ];

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
    }
}
