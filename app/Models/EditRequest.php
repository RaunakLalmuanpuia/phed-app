<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EditRequest extends Model
{
    //
    protected $fillable = [
        'employee_id',
        'previous_data',
        'requested_changes',
        'request_date',
        'approval_status',
        'approval_date',
    ];

    protected $casts = [
        'request_date' => 'date',
        'approval_date' => 'date',
        'requested_changes' => 'array',
        'previous_data' => 'array',
    ];

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
    }
    public function attachments(): HasMany
    {
        return $this->hasMany(EditRequestDocument::class);
    }


}
