<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SingleActionController extends Controller
{
    /**
     * Handle the incoming request.
     * 
     * This is single action controller, which means it only has one method to handle requests.
     * It is useful for simple tasks or when you want to keep your code organized without creating
     * a full controller class with multiple methods.
     */
    public function __invoke(Request $request)
    {
        return view('single action controller');
    }
}
