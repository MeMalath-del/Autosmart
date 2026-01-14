<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VinSearch extends Model
{
    protected $fillable = ['vin', 'user_id', 'vin_lookup_id', 'results_count'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vinLookup(): BelongsTo
    {
        return $this->belongsTo(VinLookup::class);
    }
}
