<?php

namespace App\GraphQL\Mutations;

use App\Models\Server;

class StopVideoStream
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $server = Server::findOrFail($args['server_id']);

        return app(\App\Actions\StopVideoStream::class)->execute($server);

    }
}
