<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;

class FonnteService
{
    public function sendText(string $target, string $message): array
    {
        $baseUrl = rtrim(config('services.fonnte.base_url'), '/');
        $token = config('services.fonnte.token');

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->asForm()->post($baseUrl . '/send', [
            'target' => $target,
            'message' => $message,
        ]);

        return [
            'ok' => $response->successful(),
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body(),
        ];
    }
}