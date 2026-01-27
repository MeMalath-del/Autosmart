<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceQuote extends Model
{
    protected $fillable = [
        'maintenance_request_id', 'workshop_id', 'labor_cost', 'parts_cost', 'total',
        'description', 'estimated_hours', 'available_date', 'status', 'expires_at',
    ];

    protected $casts = ['labor_cost' => 'decimal:2', 'parts_cost' => 'decimal:2', 'total' => 'decimal:2',
        'available_date' => 'date', 'expires_at' => 'datetime'];

    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function accept(): void
    {
        $this->update(['status' => 'accepted']);
    }

    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }
}
