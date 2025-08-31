<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentEditRequestFile extends Model
{
    //

    protected $fillable = [
        'document_edit_request_id', 'document_type_id', 'path','mime', 'name','type',
    ];

    public function request()
    {
        return $this->belongsTo(DocumentEditRequest::class, 'document_edit_request_id');
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
}
