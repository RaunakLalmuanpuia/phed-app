<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class RemunerationController extends Controller
{
    //
    public function store(Request $request, Employee $model){

        $validated = $request->validate([
            'remuneration' => 'required|numeric|min:0',
            'next_increment_date' => 'required|date'
        ]);

        $model->remunerationDetail()->create($validated);

        return redirect()->back()->with('success', 'Remuneration successfully recorded.');
    }
    public function update(Request $request, Employee $model)
    {
        $validated = $request->validate([
            'remuneration' => 'required|numeric|min:0',
            'next_increment_date' => 'required|date'
        ]);

        $remunerationDetail = $model->remunerationDetail; // Get model instance

        if ($remunerationDetail) {
            $remunerationDetail->fill($validated);
            $remunerationDetail->save(); // This triggers the saving event in boot()
        }

        return back()->with('success', 'Remuneration successfully updated.');
    }
}
