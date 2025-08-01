<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Office extends Model
{
    protected $fillable = ['name', 'type','location'];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
