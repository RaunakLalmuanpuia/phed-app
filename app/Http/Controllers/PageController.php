<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    //

    public function contact(Request $request)
    {
        return inertia('Frontend/Page/Contact', [
        ]);
    }

    public function privacy(Request $request)
    {
        return inertia('Frontend/Page/Privacy', [
        ]);
    }
    public function term(Request $request)
    {
        return inertia('Frontend/Page/Term', [
        ]);
    }
}
