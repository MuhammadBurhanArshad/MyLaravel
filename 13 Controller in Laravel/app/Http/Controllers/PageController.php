<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function showUser($id) {
        // return '<h1>Welcome to First Controller</h1>';
        return view('user', ['id' => $id]);
    }
}
