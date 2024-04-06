<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class UserExperiment extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'experiment_id',
        'device_id',
        'schema_id',
        'demo_id',
        'input',
        'output',
        'note',
        'simulation_time',
        'sampling_rate',
        'filled',
        'remote_id'
    ];

    protected $casts = [
        'input' => 'array',
        'output' => 'array',
    ];

    public function getResultAttribute(): ?String
    {
        return isset($this->getMedia('result')[0])
            ? $this->getMedia('result')[0]->getFullUrl()
            : null;
    }

    public function getEvaluationAttribute(?string $value): ?array
    {
        return $value ? collect(json_decode($value, true))->recursiveCollect()
            ->map(fn(Collection $evaluation) => $evaluation
                ->map(fn(float $item, string $key) => ['name' => $key, 'value' => round($item, 3)])
            )
            ->toArray() : null;
    }

    // **************************** SCOPES **************************** //

    public function scopeExecuted(Builder $query, ?bool $forAuthUser = true): Builder
    {
        if($forAuthUser) $query->where('user_id', auth()->user()->id);

        return $query->where('remote_id', '!=', null);
    }

    public function scopeUnfinished(Builder $query, ?bool $forAuthUser = true): Builder
    {
        if($forAuthUser) $query->where('user_id', auth()->user()->id);

        return $query->where([
            ['filled', null],
            ['deleted_at', null]
        ]);
    }

    public function scopeFinished(Builder $query, array $userId = []): Builder
    {
        if ($userId) $query->whereIn('user_id', $userId);

        return $query->where([
            ['filled', true],
            ['deleted_at', null]
        ]);
    }

    public function scopeUnevaluated(Builder $query): Builder
    {
        return $query->whereNull('evaluation');
    }


    public function scopeFilterDevice(Builder $query, int $deviceId): Builder
    {
        return $query->whereHas('experiment', function($q) use ($deviceId) {
            $q->where('device_id', $deviceId);
        });
    }

    // **************************** MEDIA **************************** //

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('result')
//            ->acceptsMimeTypes([
//            ])
            ->singleFile();
    }

    // **************************** RELATIONS **************************** //

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function experiment(): BelongsTo
    {
        return $this->belongsTo(Experiment::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function schema(): BelongsTo
    {
        return $this->belongsTo(Schema::class);
    }

    public function demo(): BelongsTo
    {
        return $this->belongsTo(Demo::class);
    }
}
