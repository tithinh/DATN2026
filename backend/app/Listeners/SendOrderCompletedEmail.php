<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Mail\OrderCompleted as OrderCompletedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOrderCompletedEmail implements ShouldQueue
{
    public int $tries = 2;
    public int $backoff = 10;

    public function handle(OrderCompleted $event): void
    {
        $order = $event->order;

        if (empty($order->customer_email)) {
            Log::warning("OrderCompleted: no email for order {$order->order_code}, skipping.");
            return;
        }

        Mail::to($order->customer_email)->send(new OrderCompletedMail($order));
    }

    public function failed(OrderCompleted $event, \Throwable $exception): void
    {
        Log::error("SendOrderCompletedEmail failed for order {$event->order->order_code}: {$exception->getMessage()}");
    }
}
