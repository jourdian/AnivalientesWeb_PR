<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushService
{
    /**
     * Envía una notificación push mediante Expo.
     *
     * @param string $token ExpoPushToken del usuario
     * @param string $title Título de la notificación
     * @param string $body Cuerpo de la notificación
     * @param array $data Datos personalizados adicionales
     */
    public static function send(string $token, string $title, string $body, array $data = []): void
    {
        if (!str_starts_with($token, 'ExponentPushToken')) {
            Log::warning("Token inválido: $token");
            return;
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate',
                'Content-Type' => 'application/json',
            ])->post('https://exp.host/--/api/v2/push/send', [
                'to' => $token,
                'sound' => 'default',
                'title' => $title,
                'body' => $body,
                'channelId' => 'default', 
                'data' => $data,
            ]);

            if ($response->failed()) {
                Log::error('Error al enviar notificación Expo: ' . $response->body());
            } else {
                Log::info('Notificación Expo enviada: ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('Excepción al enviar notificación Expo: ' . $e->getMessage());
        }
    }
}
