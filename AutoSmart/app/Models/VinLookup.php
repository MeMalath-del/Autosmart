<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VinLookup extends Model
{
    protected $fillable = ['vin', 'make', 'model', 'year', 'engine', 'transmission', 'body_type', 'fuel_type', 'raw_data'];
    protected $casts = ['raw_data' => 'array'];

    public function getDisplayNameAttribute(): string
    {
        return trim("{$this->year} {$this->make} {$this->model}");
    }

    public static function findOrDecode(string $vin): ?self
    {
        $existing = self::where('vin', strtoupper($vin))->first();
        if ($existing) return $existing;

        // In production, call external VIN decoder API
        // For now, decode basic info from VIN
        $decoded = self::decodeVin($vin);
        if ($decoded) {
            return self::create(array_merge(['vin' => strtoupper($vin)], $decoded));
        }
        return null;
    }

    protected static function decodeVin(string $vin): ?array
    {
        if (strlen($vin) !== 17) return null;
        
        // Basic VIN decoding (positions 1-3: WMI, 10: Year)
        $wmi = substr($vin, 0, 3);
        $yearCode = $vin[9];
        
        $makes = ['1G1' => 'Chevrolet', '1G6' => 'Cadillac', '1FA' => 'Ford', '1FM' => 'Ford', 
                  'JTD' => 'Toyota', '5TD' => 'Toyota', 'JHM' => 'Honda', '5FN' => 'Honda',
                  'WBA' => 'BMW', 'WDB' => 'Mercedes-Benz', 'WAU' => 'Audi'];
        
        $years = ['A' => 2010, 'B' => 2011, 'C' => 2012, 'D' => 2013, 'E' => 2014, 
                  'F' => 2015, 'G' => 2016, 'H' => 2017, 'J' => 2018, 'K' => 2019,
                  'L' => 2020, 'M' => 2021, 'N' => 2022, 'P' => 2023, 'R' => 2024];

        return [
            'make' => $makes[$wmi] ?? null,
            'year' => $years[$yearCode] ?? null,
        ];
    }
}
