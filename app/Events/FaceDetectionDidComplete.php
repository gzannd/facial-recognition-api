<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FaceDetectionDidComplete
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $imageData; //Base64 representation of the image
    public $imageId;   //System generated ID of image
    public $deviceId;  //ID of device that generated the image
    public $geometry;  //Canonical face detection geometry

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($image, $imageId, $deviceId, $geometry)
    {
        $this->imageData = $image;
        $this->imageId = $imageId;
        $this->deviceId = $deviceId;
        $this->geometry = $geometry;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
