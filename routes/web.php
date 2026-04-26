<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ActivityTemplateController;
use App\Http\Controllers\CalendarEventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('calendar.dashboard');
});

Route::get('/admin/dashboard', [CalendarEventController::class, 'dashboard'])->name('calendar.dashboard');

Route::resource('employees', EmployeeController::class);
Route::resource('activity-templates', ActivityTemplateController::class);

Route::prefix('calendar')->name('calendar.')->group(function () {
    Route::get('/events', [CalendarEventController::class, 'events'])->name('events');
    Route::post('/events', [CalendarEventController::class, 'store'])->name('events.store');
    Route::get('/events/{calendarEvent}', [CalendarEventController::class, 'show'])->name('events.show');
    Route::put('/events/{calendarEvent}', [CalendarEventController::class, 'update'])->name('events.update');
    Route::patch('/events/{calendarEvent}/date', [CalendarEventController::class, 'updateDate'])->name('events.update-date');
    Route::delete('/events/{calendarEvent}', [CalendarEventController::class, 'destroy'])->name('events.destroy');
});