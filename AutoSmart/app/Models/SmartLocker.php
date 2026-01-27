<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartLocker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'latitude',
        'longitude',
        'total_compartments',
        'available_compartments',
        'compartment_sizes',
        'working_hours',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'compartment_sizes' => 'array',
        'working_hours' => 'array',
        'is_active' => 'boolean',
    ];

    public function reservations()
    {
        return $this->hasMany(LockerReservation::class, 'locker_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function hasAvailableCompartment($size = 'medium')
    {
        return $this->available_compartments > 0;
    }

    public function reserveCompartment($order, $size = 'medium')
    {
        if (! $this->hasAvailableCompartment($size)) {
            throw new \Exception('No compartments available');
        }

        $this->decrement('available_compartments');

        return $this->reservations()->create([
            'order_id' => $order->id,
            'compartment_number' => $this->generateCompartmentNumber(),
            'compartment_size' => $size,
            'access_code' => $this->generateAccessCode(),
            'reserved_until' => now()->addDays(3),
            'status' => 'reserved',
        ]);
    }

    protected function generateCompartmentNumber()
    {
        return sprintf('C%03d', rand(1, $this->total_compartments));
    }

    protected function generateAccessCode()
    {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function getCoordinatesAttribute()
    {
        return [
            'lat' => $this->latitude,
            'lng' => $this->longitude,
        ];
    }
}
