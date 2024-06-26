<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'output_value',
        'argument_id',
    ];

    // **************************** RELATIONS **************************** //

    public function argument(): BelongsTo
    {
        return $this->belongsTo(Argument::class);
    }
}
