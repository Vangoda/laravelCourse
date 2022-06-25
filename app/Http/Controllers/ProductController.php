<?php

namespace App\Http\Controllers;

use App\Events\ProductUpdatedEvent;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
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

        // Fire the event to update cache
        event(new ProductUpdatedEvent);

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

    /**
     * @param Request $request 
     * @return array 
     */
    public function backend(Request $request){
        
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 2);

        $products = Cache::remember('productsBackend', 1800, 
            fn() => Product::all()
        );
        /**
         * @var Collection $products
         * @var int $page
         * @var int $limit
         */

        // Implementing search
        if ($request->has('searchTerm')){
            // Set search term
            $searchTerm = $request->input('searchTerm');
            // Filter
            $products = $products->filter( fn ( Product $product) =>
                Str::contains($product->title, $searchTerm) ||
                Str::contains($product->description, $searchTerm)
            );
        }

        // Implementing sort
        if($request->has('sort')){
            $descending = strtolower($request->input('sort')) == "desc" ? true : false;

            // Determine column to sort by. Default by price
            $sortBy = $request->has('sortBy') ? $request->input('sortBy') : 'price';

            $products = $products->sortBy($sortBy, SORT_NATURAL, $descending);
        }

        // Get total after filtering
        $total = $products->count();
        // Calculate last page
        $lastPage = ceil($total/$limit);

        // Set page to last page if greater
        $page = $page > $lastPage ? $lastPage : $page;

        return [
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $limit,
                'last_page' => ceil($total/$limit)
            ],
            'data' => $products->forPage($page, $limit)->values()
        ];
    }
}
