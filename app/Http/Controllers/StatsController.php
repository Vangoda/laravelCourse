<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index(Request $request){
        // Get the authenticated user
        $user = $request->user();

        /** @var User $user */
        // Get user related links
        $links = Link::where('user_id', $user->id)->get();

        // Customize fetched links
        return $links->map(function (Link $link){
            // Fetch count and revenue for individual links
            $orders = Order::where('code', $link->code)
            ->where('complete', 1)
            ->get();
            $count = $orders->count();
            $revenue = $orders->sum(
                fn(Order $order) => $order->ambassador_revenue
            );

            return [
                'code'=> $link->code,
                'count'=> $count,
                'revenue' => $revenue
            ];
        });
    }

    /** 
     * Returns the ambassadors sorted by their revenue
     * @return void  */
    public function rankings(){
        // Get ambassadors
        $ambassadors = User::ambassadors()->get();

        // Create rankings array
        $rankings = $ambassadors->map(fn (User $ambassador) => [
            'name' => $ambassador->name,
            'revenue' => $ambassador->revenue
        ]);
        
        // Sort the rankings and return collection
        return $rankings->sortBy('revenue', SORT_NUMERIC, true)->values();
    }
}
