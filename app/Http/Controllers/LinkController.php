<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index($id)
    {
        // Query Link models which match user id.
        return Link::whereUserId($id)->get();
    }
}
