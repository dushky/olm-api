<?php

namespace App\GraphQL\Queries;

use App\Actions\GetCameraStatus;
use App\Actions\GetVideoStreamStatus;
use App\Models\Server;

class CameraStatus
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {

        $server = Server::findOrFail($args['server_id']);
        return app(GetCameraStatus::class)->execute($server);
    }
}
