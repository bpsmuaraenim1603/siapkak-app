<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'phone',
    ];

    public function calendarEvents(): BelongsToMany
    {
        return $this->belongsToMany(
            CalendarEvent::class,
            'calendar_event_employees'
        )->withPivot('sort_order')->withTimestamps();
    }
}
