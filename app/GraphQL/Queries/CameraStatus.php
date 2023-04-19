<?php

namespace App\GraphQL\Queries;

use App\Actions\GetCameraStatus;
use App\Models\Device;

class CameraStatus
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $device = Device::findOrFail($args['device_id']);
        return app(GetCameraStatus::class)->execute($device);
    }
}
