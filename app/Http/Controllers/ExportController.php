<?php

namespace App\Http\Controllers;

use App\Exports\UserExperimentResultExport;
use App\Models\UserExperiment;
class ExportController extends Controller
{
    public function exportUserExperimentResult(UserExperiment $userExperiment){
        return new UserExperimentResultExport($userExperiment);
    }
}
