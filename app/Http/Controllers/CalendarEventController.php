<?php

namespace App\Http\Controllers;

use App\Jobs\SendScheduledWhatsAppJob;
use App\Models\ActivityTemplate;
use App\Models\CalendarEvent;
use App\Models\Employee;
use App\Models\WhatsAppGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarEventController extends Controller
{
    public function dashboard()
    {
        $activityTemplates = ActivityTemplate::orderBy('name')->get();
        $employees = Employee::orderBy('name')->get();
        $whatsappGroups = WhatsAppGroup::orderBy('name')->get();

        return view('calendar.dashboard', compact('activityTemplates', 'employees', 'whatsappGroups'));
    }

    public function events()
    {
        $events = CalendarEvent::with(['activityTemplate', 'employees', 'whatsappGroups'])
            ->orderBy('event_date')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => (string) $event->id,
                    'title' => $event->title,
                    'start' => $event->event_date ? $event->event_date->format('Y-m-d') : null,
                    'allDay' => true,
                    'backgroundColor' => match ($event->whatsapp_status) {
                        'sent' => '#16a34a',
                        'failed' => '#dc2626',
                        'queued' => '#eab308',
                        default => '#2563eb',
                    },
                    'borderColor' => match ($event->whatsapp_status) {
                        'sent' => '#16a34a',
                        'failed' => '#dc2626',
                        'queued' => '#eab308',
                        default => '#2563eb',
                    },
                    'extendedProps' => [
                        'activity_template_id' => $event->activity_template_id,
                        'notes' => $event->notes,
                        'employee_ids' => $event->employees->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
                        'employee_names' => $event->employees->pluck('name')->values()->all(),
                        'whatsapp_group_ids' => $event->whatsappGroups->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
                        'whatsapp_group_names' => $event->whatsappGroups->pluck('name')->values()->all(),
                        'start_datetime' => $event->start_datetime ? $event->start_datetime->format('Y-m-d\TH:i') : null,
                        'send_at' => $event->send_at ? $event->send_at->format('Y-m-d\TH:i') : null,
                        'send_mode' => $event->send_mode,
                        'whatsapp_status' => $event->whatsapp_status,
                        'whatsapp_message_snapshot' => $event->whatsapp_message_snapshot,
                        'whatsapp_error_message' => $event->whatsapp_error_message,
                        'whatsapp_message_template' => $event->activityTemplate?->whatsapp_message,
                        'needs_employee_list' => $event->activityTemplate?->needs_employee_list,
                    ],
                ];
            })
            ->values();

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_template_id' => ['required', 'exists:activity_templates,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'send_at' => ['nullable', 'date'],
            'send_mode' => ['required', 'in:employees,groups,both'],
            'notes' => ['nullable', 'string'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['exists:employees,id'],
            'whatsapp_group_ids' => ['nullable', 'array'],
            'whatsapp_group_ids.*' => ['exists:whatsapp_groups,id'],
        ]);

        $template = ActivityTemplate::findOrFail($validated['activity_template_id']);

        DB::transaction(function () use ($validated, $template) {
            $startDatetime = null;

            if (!empty($validated['start_time'])) {
                $startDatetime = $validated['event_date'] . ' ' . $validated['start_time'] . ':00';
            }

            $event = CalendarEvent::create([
                'activity_template_id' => $validated['activity_template_id'],
                'title' => $validated['title'] ?: $template->name,
                'event_date' => $validated['event_date'],
                'start_datetime' => $startDatetime,
                'send_at' => $validated['send_at'] ?? null,
                'send_mode' => $validated['send_mode'],
                'notes' => $validated['notes'] ?? null,
                'whatsapp_status' => 'pending',
                'whatsapp_message_snapshot' => $template->whatsapp_message,
            ]);

            $employeeSyncData = [];
            foreach (($validated['employee_ids'] ?? []) as $index => $employeeId) {
                $employeeSyncData[$employeeId] = [
                    'sort_order' => $index + 1,
                ];
            }

            $event->employees()->sync($employeeSyncData);
            $event->whatsappGroups()->sync($validated['whatsapp_group_ids'] ?? []);
        });

        return response()->json([
            'message' => 'Event berhasil ditambahkan.',
        ]);
    }

    public function show(CalendarEvent $calendarEvent)
    {
        $calendarEvent->load(['activityTemplate', 'employees', 'whatsappGroups']);

        return response()->json([
            'id' => $calendarEvent->id,
            'activity_template_id' => $calendarEvent->activity_template_id,
            'title' => $calendarEvent->title,
            'event_date' => $calendarEvent->event_date ? $calendarEvent->event_date->format('Y-m-d') : null,
            'start_time' => $calendarEvent->start_datetime ? $calendarEvent->start_datetime->format('H:i') : null,
            'send_at' => $calendarEvent->send_at ? $calendarEvent->send_at->format('Y-m-d\TH:i') : null,
            'send_mode' => $calendarEvent->send_mode,
            'notes' => $calendarEvent->notes,
            'whatsapp_status' => $calendarEvent->whatsapp_status,
            'whatsapp_error_message' => $calendarEvent->whatsapp_error_message,
            'employee_ids' => $calendarEvent->employees->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
            'whatsapp_group_ids' => $calendarEvent->whatsappGroups->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
        ]);
    }

    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        $validated = $request->validate([
            'activity_template_id' => ['required', 'exists:activity_templates,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'send_at' => ['nullable', 'date'],
            'send_mode' => ['required', 'in:employees,groups,both'],
            'notes' => ['nullable', 'string'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['exists:employees,id'],
            'whatsapp_group_ids' => ['nullable', 'array'],
            'whatsapp_group_ids.*' => ['exists:whatsapp_groups,id'],
        ]);

        $template = ActivityTemplate::findOrFail($validated['activity_template_id']);

        DB::transaction(function () use ($validated, $template, $calendarEvent) {
            $startDatetime = null;

            if (!empty($validated['start_time'])) {
                $startDatetime = $validated['event_date'] . ' ' . $validated['start_time'] . ':00';
            }

            $calendarEvent->update([
                'activity_template_id' => $validated['activity_template_id'],
                'title' => $validated['title'] ?: $template->name,
                'event_date' => $validated['event_date'],
                'start_datetime' => $startDatetime,
                'send_at' => $validated['send_at'] ?? null,
                'send_mode' => $validated['send_mode'],
                'notes' => $validated['notes'] ?? null,
                'whatsapp_status' => 'pending',
                'whatsapp_message_snapshot' => $template->whatsapp_message,
                'whatsapp_error_message' => null,
            ]);

            $employeeSyncData = [];
            foreach (($validated['employee_ids'] ?? []) as $index => $employeeId) {
                $employeeSyncData[$employeeId] = [
                    'sort_order' => $index + 1,
                ];
            }

            $calendarEvent->employees()->sync($employeeSyncData);
            $calendarEvent->whatsappGroups()->sync($validated['whatsapp_group_ids'] ?? []);
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

        $startDatetime = $calendarEvent->start_datetime;

        if ($startDatetime) {
            $timePart = $startDatetime->format('H:i:s');
            $startDatetime = $validated['event_date'] . ' ' . $timePart;
        }

        $calendarEvent->update([
            'event_date' => $validated['event_date'],
            'start_datetime' => $startDatetime,
        ]);

        return response()->json([
            'message' => 'Tanggal event berhasil diperbarui.',
        ]);
    }

    public function destroy(CalendarEvent $calendarEvent)
    {
        $calendarEvent->employees()->detach();
        $calendarEvent->whatsappGroups()->detach();
        $calendarEvent->delete();

        return response()->json([
            'message' => 'Event berhasil dihapus.',
        ]);
    }

    public function sendWhatsapp(CalendarEvent $calendarEvent)
    {
        $calendarEvent->update([
            'whatsapp_status' => 'queued',
            'whatsapp_error_message' => null,
        ]);

        SendScheduledWhatsAppJob::dispatch($calendarEvent->id);

        return response()->json([
            'message' => 'Pengiriman WhatsApp dimasukkan ke antrean.',
        ]);
    }
}