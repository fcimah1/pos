<?php

namespace App\Listeners;

use App\Events\OrderReadyEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class BroadcastOrderReadyListener implements ShouldQueue
{
    public function handle(OrderReadyEvent $event)
    {
        // placeholder: could augment data or log readiness
    }
}
