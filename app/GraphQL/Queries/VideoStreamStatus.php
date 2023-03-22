<?php

namespace App\GraphQL\Queries;

use App\Actions\GetVideoStreamStatus;
use App\Models\Server;

class VideoStreamStatus
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $server = Server::findOrFail($args['server_id']);
        return app(GetVideoStreamStatus::class)->execute($server);
    }
}
