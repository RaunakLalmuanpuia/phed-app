<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeletionDetail extends Model
{

    protected $fillable = [
        'employee_id',
        'seniority_list',
        'reason',
        'year',
        'remark',
        'supporting_document',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
