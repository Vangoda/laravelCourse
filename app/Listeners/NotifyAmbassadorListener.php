<?php

namespace App\Listeners;

use App\Events\OrderCompletedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyAmbassadorListener
{
    /**
     * Handle the event.
     * Send notification email to ambassador
     *
     * @param  OrderCompletedEvent  $event
     * @return void
     */
    public function handle(OrderCompletedEvent $event)
    {
        $order = $event->order;

        Mail::send(
            'ambassador',
            ['order' => $order],
            function(Message $message) use($order){
                $message->subject("Order $order->id completed.");
                $message->to($order->ambassador_email);
            }
        );
    }
}
