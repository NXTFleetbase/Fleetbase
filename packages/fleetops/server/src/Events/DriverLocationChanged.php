<?php

namespace Fleetbase\FleetOps\Events;

use Fleetbase\FleetOps\Models\Driver;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class DriverLocationChanged implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * The event id.
     *
     * @var string
     */
    public $eventId;

    /**
     * The datetime instance the broadcast ws triggered.
     *
     * @var string
     */
    public $sentAt;

    /**
     * The uuid of the driver.
     *
     * @var string
     */
    public $driverUuid;

    /**
     * The public id of the driver.
     *
     * @var string
     */
    public $driverId;

    /**
     * The internal id of the driver.
     *
     * @var string
     */
    public $driverInternalId;

    /**
     * The name of the driver.
     *
     * @var string
     */
    public $driverName;

    /**
     * The phone of the driver.
     *
     * @var string
     */
    public $driverPhone;

    /**
     * The new driver location.
     *
     * @var string
     */
    public $location;

    /**
     * The driver altitude.
     *
     * @var string
     */
    public $altitude;

    /**
     * The ndriver heading.
     *
     * @var string
     */
    public $heading;

    /**
     * The driver speed.
     *
     * @var string
     */
    public $speed;

    /**
     * Optional, additional data.
     *
     * @var array
     */
    public $additionalData = [];

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Driver $driver, array $additionalData = [])
    {
        $this->eventId          = uniqid('event_');
        $this->sentAt           = Carbon::now()->toDateTimeString();
        $this->additionalData   = $additionalData;
        $this->driverUuid       = $driver->uuid;
        $this->driverId         = $driver->public_id;
        $this->driverInternalId = $driver->internal_id;
        $this->driverName       = $driver->name;
        $this->driverPhone      = $driver->phone;
        $this->location         = $driver->location;
        $this->altitude         = $driver->altitude;
        $this->heading          = $driver->heading;
        $this->speed            = $driver->speed;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new Channel('company.' . session('company')),
            new Channel('api.' . session('api_credential')),
            new Channel('driver.' . $this->driverId),
            new Channel('driver.' . $this->driverUuid),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'driver.location_changed';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id'          => $this->eventId,
            'api_version' => config('api.version'),
            'event'       => $this->broadcastAs(),
            'created_at'  => $this->sentAt,
            'data'        => [
                'id'             => $this->driverId,
                'internal_id'    => $this->driverInternalId,
                'name'           => $this->driverName,
                'phone'          => $this->driverPhone,
                'location'       => $this->location,
                'altitude'       => $this->altitude,
                'heading'        => $this->heading,
                'speed'          => $this->speed,
                'additionalData' => $this->additionalData,
            ],
        ];
    }
}
