<?php

namespace App\Actions;

use App\Models\UserExperiment;
use Illuminate\Support\Collection;
use function Symfony\Component\Translation\t;

class EvaluateUserExperiment
{
    public function execute(UserExperiment $userExperiment): void
    {
        if ($userExperiment->schema?->type === 'control') {
            $this->runControlEvaluation($userExperiment);
        } else {
            $this->runIdentEvaluation($userExperiment);
        }
    }

    protected function runControlEvaluation(UserExperiment $userExperiment): void
    {

        $input = collect($userExperiment->input[0]['input'][0]);
        $output = collect($userExperiment->output)
            ->pluck('data', 'name')
            ->recursiveCollect();

        $regRegulate = $input->pluck('output_value', 'name')['reg_regulate'];
        $Ts = (float)$input->pluck('value', 'name')['s_rate'] / 1000;

        /** @var Collection $allTime */
        $allTime = $output['time'];
        /** @var Collection $allY */
        $allY = $output[$regRegulate];

        /** @var Collection $w */
        $requireValues = $output['set_point'];
        $finalEvaluation = $requireValues->chunkWhile(fn($value, $key, Collection $chunk) => $value === $chunk->last())
            ->reduce(function (Collection $carryEvaluation, Collection $requireValuesChunk) use($Ts, $allTime, $allY) {
                $evaluation = collect();

                $w = $requireValuesChunk->first();
                $sliceIndex = $requireValuesChunk->keys()->first();
                $sliceSize = $requireValuesChunk->count();

                $time = $allTime->slice($sliceIndex, $sliceSize);
                $y = $allY->slice($sliceIndex, $sliceSize);

                // Required value
                $evaluation['required_value'] = $w;

                // IAE, ISE
                [$evaluation['ise'], $evaluation['iae']] = $y->reduceSpread(
                    fn( float $carryISE, float $carryIAE, float $item) => [
                        $carryISE + ($w - $item) ** 2,
                        $carryIAE + abs($w - $item)
                    ],
                    0, 0
                );

                //ITAE
                $evaluation['itae'] = $time->reduce(
                    fn(float $carryITAE, float $item, int $key) => $carryITAE + ($item * abs($w - $y[$key])),
                    0
                );

                $maxY = $y->max();
                //OVERSHOOT
//                $evaluation['overshoot'] = (
//                $maxY > $w
//                    ? round(abs($w - $maxY) * 100 / $w, 3)
//                    : 0
//                );

                // STEADY STATE VALUE
                $steadyStateValue = $y->slice(-10)->average();
//                $evaluation['steady_state_value'] = $steadyStateValue;

                // PERMANENT ERROR
                $evaluation['permanent_error'] = abs($w - $steadyStateValue);

                // MAX OVERSHOOT
                $evaluation['max_overshoot'] = $maxY - $steadyStateValue;

                // SETTLING TIME +-2%
                $settling2Index = $this->getSettlingIndex($y, 0.02, $steadyStateValue);

                if ($settling2Index !== null && ++$settling2Index < $y->count()) {
                    $evaluation['settling_time_2'] = $settling2Index * $Ts;
                }

                // SETTLING TIME +-5%
                $settling5Index = $this->getSettlingIndex($y, 0.05, $steadyStateValue);

                if ($settling5Index !== null && ++$settling5Index < $y->count()) {
                    $evaluation['settling_time_5'] = $settling5Index * $Ts;
                }

                return $carryEvaluation->add($evaluation);
            }, collect());

        $userExperiment->evaluation = $finalEvaluation;
        $userExperiment->save();
    }

    protected function runIdentEvaluation(UserExperiment $userExperiment): void
    {

    }

    protected function getSettlingIndex($y, $tolerance, $steadyStateValue) {
        $tolerance = $tolerance * $steadyStateValue;
        return $y
            ->filter(fn (float $item) => abs($item - $steadyStateValue) > $tolerance)
            ->keys()
            ->last();
    }




}
