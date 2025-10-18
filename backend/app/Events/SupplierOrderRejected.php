<?php

namespace App\Events;

use App\Models\Purchase;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupplierOrderRejected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $purchase;
    public function __construct(Purchase $purchase)
    {
        //
        $this->purchase = $purchase;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new Channel('purchase');
    }

    public function broadcastAs()
    {
        return 'purchase.reject';
    }

    public function broadcastWith()
    {
        return [
            'purchase' => [
                'purchase_id' => $this->purchase->id,
                'supplier_id' => $this->purchase->supplier_id,
                'supplier_name' => $this->purchase->supplier->supplier_name
            ]
        ];
    }
}
