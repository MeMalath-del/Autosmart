<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    public static function log(string $event, Model $model, array $oldValues = [], array $newValues = []): AuditLog
    {
        return AuditLog::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);
    }

    public static function logCreate(Model $model): AuditLog
    {
        return self::log('created', $model, [], $model->toArray());
    }

    public static function logUpdate(Model $model): AuditLog
    {
        return self::log('updated', $model, $model->getOriginal(), $model->getChanges());
    }

    public static function logDelete(Model $model): AuditLog
    {
        return self::log('deleted', $model, $model->toArray(), []);
    }

    public static function logLogin(): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'event' => 'login',
            'auditable_type' => 'App\\Models\\User',
            'auditable_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);
    }

    public function getLogsForModel(Model $model): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::where('auditable_type', get_class($model))
            ->where('auditable_id', $model->getKey())
            ->with('user')
            ->latest()
            ->get();
    }

    public function getUserActivity(int $userId, int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->latest()
            ->limit(100)
            ->get();
    }
}
