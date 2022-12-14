<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\User;

class chatNotificate implements shouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $reciever;
    private $data;

    public function __construct($reciever, $data)
    {
        $this->reciever = User::find($reciever);
        $this->data = Array($data);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.User.'.$this->reciever->id);
    }

    public function broadcastWith(){
        return $this->data;
    }
}
