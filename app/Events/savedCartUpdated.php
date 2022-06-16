<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

use App\Models\pos_saved_cart_desktop;

class savedCartUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $savedCartCount;

    public function __construct()
    {
        //
        if (session('store')) {
            foreach (session('store') as $id_store => $choosenStore) {
                $PC = $choosenStore['id'];
                $storeID = $choosenStore['store_id'];
                $storeName = $choosenStore['name'];
            }
        }

        $cashierID = Auth::guard('cashier')->user()->id;
        $this->savedCartCount = pos_saved_cart_desktop::where('store_id', $storeID)
        ->where('pc_id', $PC)
        ->where('cashier_id', $cashierID)
        ->whereDate('created_at', today())
        ->count();

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('savedCartCount');
    }
}
