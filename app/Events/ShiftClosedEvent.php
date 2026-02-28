<?php

namespace App\Events;

use App\Models\Shift;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ShiftClosedEvent implements ShouldBroadcast
{
    use Dispatchable;

    public function __construct(public Shift $shift) {}

    public function broadcastOn(): array
    {
        return [new Channel('branch.'.$this->shift->branch_id)];
    }

    public function broadcastAs(): string
    {
        return 'shift.closed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->shift->id,
            'total_sales' => $this->shift->total_sales,
            'expected_cash' => $this->shift->expected_cash,
            'closing_cash' => $this->shift->closing_cash,
            'variance' => $this->shift->variance,
        ];
    }
}
