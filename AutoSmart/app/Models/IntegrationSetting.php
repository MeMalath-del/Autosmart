<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationSetting extends Model
{
    protected $fillable = ['provider', 'name', 'credentials', 'settings', 'is_active'];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public static function getByProvider(string $provider): ?self
    {
        return self::where('provider', $provider)->first();
    }

    public function getCredential(string $key, $default = null)
    {
        return $this->credentials[$key] ?? $default;
    }
}
