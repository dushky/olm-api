<?php

namespace App\Console\Commands;

use App\Actions\EvaluateUserExperiment;
use App\Models\UserExperiment;
use Illuminate\Console\Command;

class EvaluateUserExperiments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-experiments:evaluate {id?*} {--U|user=*} {--O|overwrite}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Evaluate user experiments output';

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
    public function handle(EvaluateUserExperiment $evaluator): void
    {
        $userExperiments = UserExperiment::finished($this->option('user'));

        $userExperimentsIds = $this->argument('id');
        if ($userExperimentsIds) {
            $userExperiments->whereIn('id', $userExperimentsIds);
        } elseif (!$this->option('overwrite')) {
            $userExperiments->unevaluated();
        }

        $userExperiments = $userExperiments->get()->toBase();
        if ($userExperiments->isEmpty()) {
            $this->warn('Nothing to evaluate. Try to edit your command arguments and options.');
            return;
        }

        $userExperiments->each(fn(UserExperiment $userExperiment) => $evaluator->execute($userExperiment));

        $this->info("User experiments evaluation completed.");

    }
}
