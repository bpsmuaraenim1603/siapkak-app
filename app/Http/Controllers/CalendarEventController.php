<?php

namespace App\Http\Controllers;

use App\Models\ActivityTemplate;
use App\Models\CalendarEvent;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarEventController extends Controller
{
    public function dashboard()
    {
        $activityTemplates = ActivityTemplate::orderBy('name')->get();
        $employees = Employee::orderBy('name')->get();

        return view('calendar.dashboard', compact('activityTemplates', 'employees'));
    }

    public function events()
    {
        $events = CalendarEvent::with(['activityTemplate', 'employees'])
            ->orderBy('event_date')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->event_date->format('Y-m-d'),
                    'allDay' => true,
                    'extendedProps' => [
                        'activity_template_id' => $event->activity_template_id,
                        'notes' => $event->notes,
                        'employee_ids' => $event->employees->pluck('id')->values(),
                        'employee_names' => $event->employees->pluck('name')->values(),
                        'whatsapp_message' => $event->activityTemplate?->whatsapp_message,
                        'needs_employee_list' => $event->activityTemplate?->needs_employee_list,
                    ],
                ];
            });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_template_id' => ['required', 'exists:activity_templates,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['exists:employees,id'],
        ]);

        $template = ActivityTemplate::findOrFail($validated['activity_template_id']);

        DB::transaction(function () use ($validated, $template) {
            $event = CalendarEvent::create([
                'activity_template_id' => $validated['activity_template_id'],
                'title' => $validated['title'] ?: $template->name,
                'event_date' => $validated['event_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $employeeSyncData = [];
            foreach (($validated['employee_ids'] ?? []) as $index => $employeeId) {
                $employeeSyncData[$employeeId] = [
                    'sort_order' => $index + 1,
                ];
            }

            $event->employees()->sync($employeeSyncData);
        });

        return response()->json([
            'message' => 'Event berhasil ditambahkan.',
        ]);
    }

    public function show(CalendarEvent $calendarEvent)
    {
        $calendarEvent->load(['activityTemplate', 'employees']);

        return response()->json([
            'id' => $calendarEvent->id,
            'activity_template_id' => $calendarEvent->activity_template_id,
            'title' => $calendarEvent->title,
            'event_date' => $calendarEvent->event_date->format('Y-m-d'),
            'notes' => $calendarEvent->notes,
            'employee_ids' => $calendarEvent->employees->pluck('id')->values(),
        ]);
    }

    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        $validated = $request->validate([
            'activity_template_id' => ['required', 'exists:activity_templates,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['exists:employees,id'],
        ]);

        $template = ActivityTemplate::findOrFail($validated['activity_template_id']);

        DB::transaction(function () use ($validated, $template, $calendarEvent) {
            $calendarEvent->update([
                'activity_template_id' => $validated['activity_template_id'],
                'title' => $validated['title'] ?: $template->name,
                'event_date' => $validated['event_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $employeeSyncData = [];
            foreach (($validated['employee_ids'] ?? []) as $index => $employeeId) {
                $employeeSyncData[$employeeId] = [
                    'sort_order' => $index + 1,
                ];
            }

            $calendarEvent->employees()->sync($employeeSyncData);
        });

        return response()->json([
            'message' => 'Event berhasil diperbarui.',
        ]);
    }

    public function updateDate(Request $request, CalendarEvent $calendarEvent)
    {
        $validated = $request->validate([
            'event_date' => ['required', 'date'],
        ]);

        $calendarEvent->update([
            'event_date' => $validated['event_date'],
        ]);

        return response()->json([
            'message' => 'Tanggal event berhasil diperbarui.',
        ]);
    }

    public function destroy(CalendarEvent $calendarEvent)
    {
        $calendarEvent->employees()->detach();
        $calendarEvent->delete();

        return response()->json([
            'message' => 'Event berhasil dihapus.',
        ]);
    }
}