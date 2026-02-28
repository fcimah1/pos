<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OrderReadyEvent implements ShouldBroadcast
{
    use Dispatchable;

    public function __construct(public Order $order) {}

    public function broadcastOn(): array
    {
        return [new Channel('branch.'.$this->order->branch_id.'.kitchen')];
    }

    public function broadcastAs(): string
    {
        return 'order.ready';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'table' => $this->order->table_number,
            'status' => $this->order->status,
        ];
    }
}
