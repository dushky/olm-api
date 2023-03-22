<?php

namespace App\Actions;

use App\Exceptions\BusinessLogicException;
use App\Models\Server;
use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\Mutation;
use Illuminate\Support\Facades\Log;

class StartVideoStream
{
    protected Server $server;

    /**
     * @param Server $server
     * @throws BusinessLogicException
     */
    public function execute(Server $server): array
    {
        $this->server = $server;
        return $this->startVideoStream();
    }

    private function startVideoStream(): array {

        $mutation = new Mutation('startVideoStream');
        $mutation
            ->setSelectionSet([
                'isRunning',
                'status'
            ]);

        try {
            $client = new Client('https://' . $this->server->api_domain . '/graphql');
            $results = $client->runQuery($mutation, true)->getData()['startVideoStream'];
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
