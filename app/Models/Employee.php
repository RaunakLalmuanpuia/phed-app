<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    //
    protected $fillable = ['office_id','avatar','employee_code','name','mobile','email','address','date_of_birth','parent_name','employment_type',
        'educational_qln','technical_qln', 'designation','name_of_workplace','post_per_qualification','date_of_engagement','skill_category','skill_at_present'];
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
    public function documentRequest(): HasMany {
        return $this->hasMany(DocumentEditRequest::class);
    }

    public function transfers(): HasMany {
        return $this->hasMany(Transfer::class);
    }

    public function transferRequests(): HasMany {
        return $this->hasMany(TransferRequest::class);
    }

    public function deletionRequests(): HasMany {
        return $this->hasMany(DeletionRequest::class);
    }

    public function editRequests(): HasMany {
        return $this->hasMany(EditRequest::class);
    }

    public function deletionDetail(): HasOne
    {
        return $this->hasOne(DeletionDetail::class);
    }

    public function remunerationDetail(): HasOne
    {
        return $this->hasOne(RemunerationDetail::class);
    }

    public function engagementCard(): HasMany
    {
        return $this->hasMany(EngagementCard::class);
    }



}
