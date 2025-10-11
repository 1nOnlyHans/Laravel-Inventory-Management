<?php

namespace App\Events;

use App\Models\SaleTransaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Sold implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public $saletransaction;
    public function __construct(SaleTransaction $sale_transaction)
    {
        //
        $this->saletransaction = $sale_transaction;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new Channel('sold');
    }

    public function broadcastAs()
    {
        return 'sold.created';
    }

    public function broadcastWith()
    {
        return [
            'transaction' => [
                'id' => $this->saletransaction->id,
                'customer_name' => $this->saletransaction->customer_name ?? 'Walk-in Customer',
                'total_amount' => number_format($this->saletransaction->total_amount, 2),
                'created_by' => $this->saletransaction->user->name ?? 'Unknown User',
                'created_at' => $this->saletransaction->created_at->format('Y-m-d H:i:s'),
            ],
        ];
    }
}
