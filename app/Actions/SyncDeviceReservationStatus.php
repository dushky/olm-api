<?php

namespace App\Actions;

use App\Exceptions\BusinessLogicException;
use App\Models\Device;
use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\Mutation;
use GraphQL\RawObject;
use Illuminate\Support\Facades\Log;

class SyncDeviceReservationStatus
{

    public function execute(Device $device): void
    {
        $this->updateDeviceReservationStatus($device);

    }

    private function updateDeviceReservationStatus(Device $device): array
    {
        $client = new Client('http://' . $device->server->api_domain . '/graphql');

        $mutation = new Mutation('updateDeviceReservationStatus');

        $isReservedNow = $device->isReservedNow();

        $deviceIsReservedNow = json_encode($isReservedNow);
        $mutation
            ->setArguments([
                'deviceReservationStatusInput' => new RawObject("
                    {
                        deviceID: \"{$device->remote_id}\"
                        isReserved: $deviceIsReservedNow
                    }
                ")
            ])->setSelectionSet([
        'updatedDevicesCount'
    ]);

        try {
            $result = $client->runQuery($mutation, true)->getData()['updateDeviceReservationStatus'];
        } catch (QueryError $exception) {
            $message = '[Device ID: ' . $device->id . ', Mutation: updateDeviceReservationStatus] ERROR: ' . $exception->getErrorDetails()['message'];
            Log::channel('experiment')->info($message);
            throw new BusinessLogicException($message);
        } catch (\Throwable $exception) {
            $message = '[Device ID: ' . $device->id . ', Mutation: updateDeviceReservationStatus] ERROR: ' . $exception->getMessage();
            Log::channel('experiment')->info($message);
            throw new BusinessLogicException($message);
        }

        return $result ?? [];

    }

}
