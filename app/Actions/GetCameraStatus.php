<?php

namespace App\Actions;

use App\Exceptions\BusinessLogicException;
use App\Models\Server;
use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\Query;
use Illuminate\Support\Facades\Log;

class GetCameraStatus
{
    protected Server $server;

    /**
     * @param Server $server
     * @throws BusinessLogicException
     */
    public function execute(Server $server): array
    {
        $this->server = $server;

        return $this->getCameraStatus();

    }

    private function getCameraStatus(): array
    {
        $url = 'https://' . $this->server->api_domain . '/graphql';

        $gql = (new Query('cameraStatus'))
            ->setSelectionSet([
                'isConnected',
                'status',
                ]);

        try {
            $client = new Client($url);
            $results = $client->runQuery($gql, true)->getData()['cameraStatus'];
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
