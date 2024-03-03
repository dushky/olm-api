<?php

namespace App\Actions;

use App\Models\Argument;
use App\Models\Option;
use App\Models\Demo;

class SyncDemoArguments
{
    public function execute(Demo $demo, array $arguments): void
    {
        $demo->arguments()->delete();

        foreach ($arguments as $arg) {
            $arg['demo_id'] = $demo->id;
            $argument = Argument::create($arg);

            if (isset($arg['options']) && is_array($arg['options'])) {
                foreach ($arg['options'] as $option) {
                    $option['argument_id'] = $argument->id;
                    Option::create($option);
                }
            }
        }
    }
}
