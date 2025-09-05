<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class RemunerationDetail extends Model
{
    //

    const MEDICAL_PERCENTAGE = 4;

    protected $fillable = [
        'employee_id',
        'remuneration',
        'pay_matrix',
        'medical_percentage',
        'medical_amount',
        'total',
        'round_total',
        'next_increment_date'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Use fixed percentage
            $medicalPercentage = self::MEDICAL_PERCENTAGE;

            $model->medical_percentage = $medicalPercentage;

             // Calculate medical amount (rounded to nearest integer)
            $model->medical_amount = round(($model->remuneration * $medicalPercentage) / 100);

            // Calculate total (rounded to nearest integer)
            $model->total = round($model->remuneration + $model->medical_amount);


            // Rounding rule: nearest 10 (below 5 → down, else → up)
            $lastDigit = $model->total % 10;
            if ($lastDigit < 5) {
                $model->round_total = floor($model->total / 10) * 10;
            } else {
                $model->round_total = ceil($model->total / 10) * 10;
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

}
