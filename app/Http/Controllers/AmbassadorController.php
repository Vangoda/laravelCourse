<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AmbassadorController extends Controller
{
    public function index()
    {
        // Runs a db query to fetch user data 
        return User::where('is_admin', 0)->get();
    }
}
