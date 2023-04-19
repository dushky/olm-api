<?php

namespace App\GraphQL\Queries;

use App\Actions\GetVideoStreamStatus;
use App\Models\Device;
use App\Models\Server;

class VideoStreamStatus
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $device = Device::findOrFail($args['device_id']);
        return app(GetVideoStreamStatus::class)->execute($device);
    }
}
