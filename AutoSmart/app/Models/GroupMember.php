<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMember extends Model
{
    protected $fillable = ['group_id', 'user_id', 'role'];

    public function group(): BelongsTo { return $this->belongsTo(CommunityGroup::class, 'group_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isModerator(): bool { return in_array($this->role, ['admin', 'moderator']); }
}
