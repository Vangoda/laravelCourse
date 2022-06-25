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
}
