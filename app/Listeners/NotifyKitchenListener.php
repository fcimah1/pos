<?php

namespace App\Listeners;

use App\Events\NewOrderEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyKitchenListener implements ShouldQueue
{
    public function handle(NewOrderEvent $event)
    {
        // Example: transform order for kitchen display or push to queue
        // For now we simply dispatch a ready event simulation placeholder
        // In real use, this could send to KDS via websocket or other mechanism
    }
}
