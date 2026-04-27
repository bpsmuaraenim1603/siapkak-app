<?php

namespace App\Support;

use App\Models\CalendarEvent;
use Carbon\Carbon;

class WhatsAppMessageBuilder
{
    public static function normalizePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $phone = preg_replace('/\D+/', '', $phone);

        if (!$phone) {
            return null;
        }

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }

    public static function build(CalendarEvent $event): string
    {
        $template = $event->whatsapp_message_snapshot
            ?: ($event->activityTemplate?->whatsapp_message ?? '');

        $tanggal = $event->event_date
            ? Carbon::parse($event->event_date)->translatedFormat('d F Y')
            : '-';

        $jam = $event->start_datetime
            ? Carbon::parse($event->start_datetime)->format('H:i')
            : '-';

        $pegawai = $event->employees
            ->values()
            ->map(fn ($employee, $index) => ($index + 1) . '. ' . $employee->name)
            ->implode("\n");

        if (blank($template)) {
            $template = "Kegiatan: {nama_kegiatan}\nTanggal: {tanggal}\nJam: {jam}\n\nPetugas:\n{daftar_pegawai}";
        }

        return strtr($template, [
            '{nama_kegiatan}' => $event->title,
            '{tanggal}' => $tanggal,
            '{jam}' => $jam,
            '{daftar_pegawai}' => $pegawai ?: '-',
        ]);
    }
}