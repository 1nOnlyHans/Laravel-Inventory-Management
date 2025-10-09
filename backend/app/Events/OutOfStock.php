<?php

namespace App\Events;

use App\Models\Product;
use App\Models\StockAlert;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OutOfStock implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $product;
    public function __construct(Product $product)
    {
        //
        $this->product = $product;
        StockAlert::create([
            'product_id' => $product->id,
            'alert_message' => 'Out of Stock'
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new Channel('stock');
    }

    public function broadcastAs()
    {
        return 'stock.nostock';
    }

    public function broadcastWith()
    {
        return [
            'product' => [
                'id' => $this->product->id,
                'product_name' => $this->product->product_name,
                'stock' => $this->product->product_quantity,
            ],
        ];
    }
}
