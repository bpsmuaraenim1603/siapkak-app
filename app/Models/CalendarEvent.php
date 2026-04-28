<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CalendarEvent extends Model
{
    protected $fillable = [
        'activity_template_id',
        'title',
        'event_date',
        'start_datetime',
        'end_datetime',
        'send_at',
        'send_mode',
        'notes',
        'whatsapp_status',
        'whatsapp_sent_at',
        'whatsapp_message_snapshot',
        'whatsapp_error_message',
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'send_at' => 'datetime',
        'whatsapp_sent_at' => 'datetime',
    ];

    public function activityTemplate(): BelongsTo
    {
        return $this->belongsTo(ActivityTemplate::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'calendar_event_employees')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function whatsappGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            WhatsAppGroup::class,
            'calendar_event_whatsapp_group'
        )->withTimestamps();
    }
}