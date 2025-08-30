<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    //
    protected $fillable = ['employee_id', 'old_office_id', 'new_office_id', 'transfer_date','supporting_document'];

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
    }

    public function oldOffice(): BelongsTo {
        return $this->belongsTo(Office::class, 'old_office_id');
    }

    public function newOffice(): BelongsTo {
        return $this->belongsTo(Office::class, 'new_office_id');
    }
}
