<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewOrderEvent implements ShouldBroadcast
{
    use Dispatchable;

    public function __construct(public Order $order) {}

    public function broadcastOn(): array
    {
        return [new Channel('branch.'.$this->order->branch_id)];
    }

    public function broadcastAs(): string
    {
        return 'order.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'branch_id' => $this->order->branch_id,
            'total' => $this->order->total_amount,
            'status' => $this->order->status,
            'type' => $this->order->type,
        ];
    }
}
