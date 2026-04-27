<?php

namespace App\Console\Commands;

use App\Jobs\SendScheduledWhatsAppJob;
use App\Models\CalendarEvent;
use Illuminate\Console\Command;

class DispatchDueWhatsAppEvents extends Command
{
    protected $signature = 'whatsapp:dispatch-due-events';
    protected $description = 'Dispatch event WhatsApp jobs that are due based on send_at';

    public function handle(): int
    {
        $events = CalendarEvent::query()
            ->whereNotNull('send_at')
            ->where('send_at', '<=', now())
            ->whereIn('whatsapp_status', ['pending', 'failed'])
            ->get();

        foreach ($events as $event) {
            SendScheduledWhatsAppJob::dispatch($event->id);

            $event->update([
                'whatsapp_status' => 'queued',
            ]);
        }

        $this->info("Dispatched {$events->count()} event(s).");

        return self::SUCCESS;
    }
}