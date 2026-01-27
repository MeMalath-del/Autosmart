<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    protected $fillable = ['title', 'body', 'icon', 'url', 'segment', 'sent_count', 'sent_at'];

    protected $casts = ['sent_at' => 'datetime'];
}
