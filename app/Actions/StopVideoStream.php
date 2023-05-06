<?php

namespace App\Actions;

use App\Exceptions\BusinessLogicException;
use App\Models\Device;
use App\Models\Server;
use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\Mutation;
use Illuminate\Support\Facades\Log;

class StopVideoStream
{
    protected Server $server;

    /**
     * @throws BusinessLogicException
     */
    public function execute(Device $device): array
    {
        $this->server = $device->server;
        return $this->stopVideoStream($device->remote_id);
    }

    private function stopVideoStream($deviceId): array {

        $mutation = new Mutation('stopVideoStream');
        $mutation
            ->setArguments([
                'deviceID' => $deviceId
            ])
            ->setSelectionSet([
                'isStopped',
                'status'
            ]);

        try {
            $client = new Client('https://' . $this->server->api_domain . '/graphql');
            $results = $client->runQuery($mutation, true)->getData()['stopVideoStream'];
        } catch (QueryError $exception) {
            $message = '[Server IP: ' . $this->server->ip_address . '] ERROR: ' . $exception->getErrorDetails()['message'];
            Log::channel('server')->info($message);
            throw new BusinessLogicException($message);
        } catch (\Throwable $exception) {
            $message = '[Server IP: ' . $this->server->ip_address . '] ERROR: ' . $exception->getMessage();
            Log::channel('server')->info($message);
            throw new BusinessLogicException($message);
        }

        return $results;
    }
}
