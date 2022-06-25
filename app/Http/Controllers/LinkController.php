<?php

namespace App\Http\Controllers;

use App\Http\Resources\LinkResource;
use App\Models\Link;
use App\Models\LinkProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LinkController extends Controller
{
    /* Standard API routes
        Verb          Path                              Action  Route Name
        GET           /links                         index   links.index
        POST          /links                         store   links.store
        GET           /links/{code}               show    links.show
        PUT|PATCH     /links/{code}               update  links.update
        DELETE        /links/{code}               destroy links.destroy
    */

    public function index($id)
    {
        // Query Link models which match user id.
        // Also get related orders.
        $links = Link::with('orders')->whereUserId($id)->get();

        // Return resource
        return LinkResource::collection($links);
    }

    public function show($code){
        // Get single link

        return Link::with('user', 'products')
        ->where('code', $code)
        ->first();
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
