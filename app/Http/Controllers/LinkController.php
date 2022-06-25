<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\LinkProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LinkController extends Controller
{
    public function index($id)
    {
        // Query Link models which match user id.
        return Link::whereUserId($id)->get();
    }

    public function store(Request $request){

        $link = Link::create([
            'user_id' => $request->user()->id,
            'code' => Str::random(6)
        ]);

        foreach($request->input('products') as $productId){
            // Create LinkProduct for each product
            LinkProduct::create([
                'product_id' => $productId,
                'link_id' => $link->id
            ]);
        }

        return $link;
    }
}
