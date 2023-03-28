<?php

namespace App\Exports;

use App\Models\UserExperiment;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class UserExperimentResultExport extends StringValueBinder implements FromCollection, Responsable, WithCustomCsvSettings, WithStrictNullComparison, WithCustomValueBinder
{
    use Exportable;

    protected Collection $userExperimentResult;
    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     */
    private string $fileName = 'user-experiment-{user-experiment}-result.csv';
    /**
     * Optional headers
     */
    private array $headers = [
        'Content-Type' => 'text/csv'
    ];

    public function __construct(UserExperiment $userExperiment)
    {
        $this->fileName = Str::replace('{user-experiment}', $userExperiment->id, $this->fileName);
        $this->userExperimentResult = collect($userExperiment->output);
    }

    public function collection()
    {
        return Collection::make()->push(
            $this->userExperimentResult->pluck('name')->toArray(),
            // create rows from columns
            ...$this->userExperimentResult
            ->pluck('data')
            ->reduce(function (array $carry, array $data) {
                foreach ($data as $key => $item) {
                    $carry[$key] = array_merge($carry[$key] ?? [], [$item]);
                }
                return $carry;
            }, [])
        );
    }

    public function getCsvSettings(): array
    {
        return [
            'output_encoding' => 'UTF-8',
            'use_bom' => true,
            'enclosure' => '',
        ];
    }

}
