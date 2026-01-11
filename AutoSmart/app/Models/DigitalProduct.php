<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DigitalProduct extends Model
{
    protected $fillable = [
        'product_id', 'file_path', 'file_name', 'file_type', 'file_size',
        'download_limit', 'expiry_days', 'preview_path'
    ];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function downloads(): HasMany { return $this->hasMany(DigitalDownload::class); }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' bytes';
    }
}
