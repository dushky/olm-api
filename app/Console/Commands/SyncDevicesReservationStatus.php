<?php

namespace App\Console\Commands;

use App\Actions\SyncDeviceReservationStatus;
use App\Models\Device;
use Illuminate\Console\Command;

class SyncDevicesReservationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devices-reservation-status:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send devices reservation status to experimental server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(SyncDeviceReservationStatus $syncDeviceReservationStatus): void
    {
        $availableDevices = Device::filterAvailable()->get();

        $availableDevices->each(fn(Device $device) => $syncDeviceReservationStatus->execute($device));

        $this->info("Devices reservation status sync completed");

    }
}
