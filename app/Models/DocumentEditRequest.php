<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentEditRequest extends Model
{
    protected $fillable = [
        'employee_id','request_date', 'approval_status', 'approval_date',
    ];

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
    }

    public function files():HasMany
    {
        return $this->hasMany(DocumentEditRequestFile::class);
    }
}
