<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\NewOrderEvent;
use App\Listeners\NotifyKitchenListener;
use App\Events\OrderReadyEvent;
use App\Listeners\BroadcastOrderReadyListener;
use App\Observers\OrderObserver;
use App\Models\Order;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NewOrderEvent::class => [
            NotifyKitchenListener::class,
        ],
        OrderReadyEvent::class => [
            BroadcastOrderReadyListener::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
        Order::observe(OrderObserver::class);
    }
}
