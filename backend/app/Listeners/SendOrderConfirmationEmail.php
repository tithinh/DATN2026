<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Mail\OrderConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmationEmail implements ShouldQueue
{
    public int $tries = 2;
    public int $backoff = 10;

    public function handle(OrderCreated $event): void
    {
        $order = $event->order;

        if (empty($order->customer_email)) {
            Log::warning("OrderCreated: no email for order {$order->order_code}, skipping.");
            return;
        }

        Mail::to($order->customer_email)->send(new OrderConfirmation($order));
    }

    public function failed(OrderCreated $event, \Throwable $exception): void
    {
        Log::error("SendOrderConfirmationEmail failed for order {$event->order->order_code}: {$exception->getMessage()}");
    }
}
