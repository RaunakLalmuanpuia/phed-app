<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferRequest extends Model
{
    //
    protected $fillable = ['employee_id', 'current_office_id', 'requested_office_id', 'request_date', 'approval_status', 'approval_date','supporting_document'];

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
    }

    public function currentOffice(): BelongsTo {
        return $this->belongsTo(Office::class, 'current_office_id');
    }

    public function requestedOffice(): BelongsTo {
        return $this->belongsTo(Office::class, 'requested_office_id');
    }
}
