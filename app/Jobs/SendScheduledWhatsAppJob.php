<?php

namespace App\Jobs;

use App\Models\CalendarEvent;
use App\Services\WhatsApp\FonnteService;
use App\Support\WhatsAppMessageBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Throwable;

class SendScheduledWhatsAppJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(public int $calendarEventId)
    {
    }

    public function handle(FonnteService $fonnte): void
    {
        $event = CalendarEvent::with(['activityTemplate', 'employees'])->find($this->calendarEventId);

        if (!$event) {
            return;
        }

        if ($event->whatsapp_status === 'sent') {
            return;
        }

        $phones = $event->employees
            ->pluck('phone')
            ->map(fn ($phone) => WhatsAppMessageBuilder::normalizePhone($phone))
            ->filter()
            ->unique()
            ->values();

        if ($phones->isEmpty()) {
            $event->update([
                'whatsapp_status' => 'failed',
                'whatsapp_error_message' => 'Tidak ada nomor HP pegawai yang valid.',
            ]);
            return;
        }

        $message = WhatsAppMessageBuilder::build($event);

        $event->update([
            'whatsapp_status' => 'queued',
            'whatsapp_error_message' => null,
        ]);

        try {
            $result = $fonnte->sendText(
                $phones->implode(','),
                $message
            );

            if ($result['ok']) {
                $event->update([
                    'whatsapp_status' => 'sent',
                    'whatsapp_sent_at' => now(),
                    'whatsapp_message_snapshot' => $message,
                    'whatsapp_error_message' => null,
                ]);
            } else {
                $event->update([
                    'whatsapp_status' => 'failed',
                    'whatsapp_message_snapshot' => $message,
                    'whatsapp_error_message' => is_array($result['body'])
                        ? json_encode($result['body'], JSON_UNESCAPED_UNICODE)
                        : (string) $result['body'],
                ]);
            }
        } catch (Throwable $e) {
            $event->update([
                'whatsapp_status' => 'failed',
                'whatsapp_message_snapshot' => $message,
                'whatsapp_error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}