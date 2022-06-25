<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Link;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OrderController extends Controller
{
    /* Standard API routes
        Verb          Path             Action  Route Name
        GET           /orders          index   orders.index
        POST          /orders          store   orders.store
        GET           /orders/{code}   show    orders.show
        PUT|PATCH     /orders/{code}   update  orders.update
        DELETE        /orders/{code}   destroy orders.destroy
    */

    public function index()
    {
        // returns all the orders

        return OrderResource::collection(Order::with('orderItems')->get());
    }

    public function store(Request $request){
        // Check for required request properties
        if(!$request->has('code')){
            abort(400, 'Invalid code!');
        }
        if(!$request->has('orderItems')){
            abort(400, 'Missing order items!');
        }

        // Get the link from code
        $link = Link::where('code', $request->input('code'))->first();

        // Create new Order
        // Using try catch block to stop DB transaction on any exception
        try{
            // Start a transaction to avoid data corruption.
            DB::beginTransaction();
            $order = new Order();

            // Set order properties
            $order->code = $link->code;
            $order->user_id = $link->user_id;
            $order->ambassador_email = $link->user->email;
            $order->first_name = $request->input('firstName');
            $order->last_name = $request->input('lastName');
            $order->email = $request->input('email');
            $order->address = $request->input('address');
            $order->country = $request->input('country');
            $order->city = $request->input('city');
            $order->zip = $request->input('zip');
            
            $order->save();

            // Create new order items from products in the current order
            foreach($request->input('orderItems') as $item){
                $product = Product::find($item['product_id']);

                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_title = $product->title;
                $orderItem->price = $product->price;
                $orderItem->quantity = $item['quantity'];
                $orderItem->ambassador_revenue = ($orderItem->price*$orderItem->quantity*0.1);
                $orderItem->admin_revenue = ($orderItem->price*$orderItem->quantity*0.9);

                $orderItem->save();
            }

            // Commit if everything ran OK
            DB::commit();
        }catch(Throwable $exception){
            // Rollback on any exception
            DB::rollBack();
            // Return 400
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Data was not saved!");
        }

        return $order->load('orderItems');
    }
}
