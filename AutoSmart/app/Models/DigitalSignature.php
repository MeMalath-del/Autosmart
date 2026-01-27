<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type',
        'signable_type',
        'signable_id',
        'signature_hash',
        'ip_address',
        'user_agent',
        'signature_data',
        'signed_at',
    ];

    protected $casts = [
        'signature_data' => 'array',
        'signed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function signable()
    {
        return $this->morphTo();
    }

    public function verify()
    {
        // Verify signature hash
        $expectedHash = hash('sha256', implode('|', [
            $this->user_id,
            $this->signable_type,
            $this->signable_id,
            $this->signed_at->timestamp,
        ]));
        
        return $this->signature_hash === $expectedHash;
    }

    public static function sign($user, $document, $type, $signatureData = null)
    {
        $signature = new static([
            'user_id' => $user->id,
            'document_type' => $type,
            'signable_type' => get_class($document),
            'signable_id' => $document->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'signature_data' => $signatureData,
            'signed_at' => now(),
        ]);
        
        $signature->signature_hash = hash('sha256', implode('|', [
            $signature->user_id,
            $signature->signable_type,
            $signature->signable_id,
            $signature->signed_at->timestamp,
        ]));
        
        $signature->save();
        
        return $signature;
    }
}
