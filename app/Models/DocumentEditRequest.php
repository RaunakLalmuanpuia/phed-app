<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentEditRequest extends Model
{
    protected $fillable = [
        'document_type_id', 'employee_id','mime', 'path', 'name','type','request_date',
        'approval_status', 'approval_date',
    ];

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }
}
