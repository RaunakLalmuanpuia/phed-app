<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentEditRequest extends Model
{
    protected $fillable = [
        'document_id', 'employee_id','mime', 'path', 'name','type','upload_date','request_date',
        'approval_status', 'approval_date',
    ];

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
    }

    public function document(): BelongsTo {
        return $this->belongsTo(Document::class);
    }
}
