<?php

namespace App\Listeners;

use App\Events\OrderCompletedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyAdminListener
{
    /**
     * Handle the event.
     * Sends notification email to admin.
     *
     * @param  OrderCompletedEvent  $event
     * @return void
     */
    public function handle(OrderCompletedEvent $event)
    {
        $order = $event->order;

        Mail::send(
            'admin',
            ['order' => $order],
            function(Message $message) use($order){
                $message->subject("Order $order->id completed.");
                $message->to('admin@admin.com');
            }
        );
    }
}
