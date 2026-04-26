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
        'notes',
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    public function activityTemplate(): BelongsTo
    {
        return $this->belongsTo(ActivityTemplate::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(
            Employee::class,
            'calendar_event_employee'
        )->withPivot('sort_order')->withTimestamps()
            ->orderByPivot('sort_order');
    }
}
