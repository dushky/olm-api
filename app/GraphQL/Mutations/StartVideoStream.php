<?php

namespace App\GraphQL\Mutations;

use App\Models\Device;
use App\Models\Server;

class StartVideoStream
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $device = Device::findOrFail($args['device_id']);
        return app(\App\Actions\StartVideoStream::class)->execute($device);

    }
}
