<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class DocumentDeleteRequest extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'document_id',
        'document_type_name',
        'approval_status',
        'request_date',
        'approval_date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
