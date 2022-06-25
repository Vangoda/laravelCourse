<?php

namespace App\Listeners;

use App\Events\ProductUpdatedEvent;
use Illuminate\Support\Facades\Cache;

class ProductUpdatedListener
{
    /**
     * Handle the event.
     *
     * @param  ProductUpdatedEvent  $event
     * @return void
     */
    public function handle(ProductUpdatedEvent $event)
    {
        // Invalidate the cache
        Cache::forget('productsFronted');
        Cache::forget('productsBackend');
    }
}
