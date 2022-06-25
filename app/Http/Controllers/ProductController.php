<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Returns all products from the DB
        return Product::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Create a new product model and store it in DB
        $product = Product::create($request->only(
            'title',
            'description',
            'imageURI',
            'price'
        ));

        return response($product, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        // Return a single product by ID (laramagic)
        return $product;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        // Update existing Product model and save changes
        $product->update($request->only([
            'title',
            'description',
            'imageURI',
            'price'
        ]));

        return response($product, Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        // Deletes the existing product model and remove it from the DB
        $product->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /** @return Collection<Product>  */
    public function frontend(){
        // Returns all of the products
        // Utilize caching
        if($products = Cache::get('productsFronted')){
            return $products;
        }

        $products = Product::all();
        Cache::set('productsFronted', $products, 1800);

        return $products;
    }

    public function backend(){
        return Product::paginate();
    }
}
