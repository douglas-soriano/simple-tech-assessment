<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Fund;

class DuplicateFundWarning
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $new_fund;

    public function __construct(Fund $new_fund)
    {
        $this->new_fund = $new_fund;
    }
}
