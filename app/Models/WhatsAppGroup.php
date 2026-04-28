<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WhatsAppGroup extends Model
{
    protected $table = 'whatsapp_groups';

    protected $fillable = [
        'name',
        'group_id',
    ];

    public function calendarEvents(): BelongsToMany
    {
        return $this->belongsToMany(
            CalendarEvent::class,
            'calendar_event_whatsapp_group',
            'whatsapp_group_id',
            'calendar_event_id'
        )->withTimestamps();
    }
}