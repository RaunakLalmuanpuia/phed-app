<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditRequestDocument extends Model
{
    //
    protected $fillable = ['edit_request_id', 'document_type_id', 'employee_id', 'name', 'path', 'mime'];

    public function editRequest(): BelongsTo
    {
        return $this->belongsTo(EditRequest::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }
}
