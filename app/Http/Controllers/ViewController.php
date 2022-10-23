<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller
{
    // Serve Vue App
    public function app()
    {
        return view('admin-app');
    }
}