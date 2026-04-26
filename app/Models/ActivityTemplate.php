<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityTemplate extends Model
{
    protected $fillable = [
        'name',
        'whatsapp_message',
        'needs_employee_list',
    ];

    protected $casts = [
        'needs_employee_list' => 'boolean',
    ];

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }
}
