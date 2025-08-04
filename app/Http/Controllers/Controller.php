<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    //
    use AuthorizesRequests, ValidatesRequests;

    protected function generateEmployeeCode()
    {
        do {
            $code = 'PHED-' . rand(100000, 999999);
        } while (Employee::where('employee_code', $code)->exists());

        return $code;
    }
}
