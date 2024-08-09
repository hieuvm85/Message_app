<?php

namespace App\Events;

use App\Models\Group;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class SendMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $group;
    public $message;

    public function __construct(Group $group,Message $message)
    {
        //
        $this->group = $group;
        $this->message = $message;
    }


   /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return [
            new Channel("group-".$this->group->id)
        ];
    }

    public function broadcastAs(){
        return "sendMessage";
    }

    public function broadcastWith(){
        return [
            'message' =>$this->message,
        ];
    }
}
