<?php

use App\Http\Controllers\ActivityTemplateController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\WhatsAppGroupController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('calendar.dashboard');
    });

    Route::get('/admin/dashboard', [CalendarEventController::class, 'dashboard'])->name('calendar.dashboard');

    Route::resource('employees', EmployeeController::class);
    Route::resource('activity-templates', ActivityTemplateController::class);
    Route::resource('whatsapp-groups', WhatsAppGroupController::class);

    Route::prefix('calendar')->name('calendar.')->group(function () {
        Route::get('/events', [CalendarEventController::class, 'events'])->name('events');
        Route::post('/events', [CalendarEventController::class, 'store'])->name('events.store');
        Route::get('/events/{calendarEvent}', [CalendarEventController::class, 'show'])->name('events.show');
        Route::put('/events/{calendarEvent}', [CalendarEventController::class, 'update'])->name('events.update');
        Route::patch('/events/{calendarEvent}/date', [CalendarEventController::class, 'updateDate'])->name('events.update-date');
        Route::delete('/events/{calendarEvent}', [CalendarEventController::class, 'destroy'])->name('events.destroy');
        Route::post('/events/{calendarEvent}/send-whatsapp', [CalendarEventController::class, 'sendWhatsapp'])
            ->name('events.send-whatsapp');
    });

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});