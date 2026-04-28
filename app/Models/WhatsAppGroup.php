<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WhatsAppGroup extends Model
{
    protected $table = 'whats_app_groups';

    protected $fillable = [
        'name',
        'group_id',
    ];

    public function calendarEvents(): BelongsToMany
    {
        return $this->belongsToMany(
            CalendarEvent::class,
            'calendar_event_whatsapp_group'
        )->withTimestamps();
    }
}