<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $url;
    protected string $token;

    public function __construct()
    {
        $this->url = config('services.whatsapp.url');
        $this->token = config('services.whatsapp.token');
    }

    /**
     * Send a WhatsApp message to a specific number.
     *
     * @param string $phone The phone number (including country code)
     * @param string $message The message text
     * @return bool True if the message was sent successfully
     */
    public function sendMessage(string $phone, string $message): bool
    {
        if (empty($this->token)) {
            Log::warning("WhatsApp token is not configured. Simulating message to $phone: $message");
            return true;
        }

        try {
            $response = Http::post($this->url, [
                'token' => $this->token,
                'to' => $phone,
                'body' => $message,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error("Failed to send WhatsApp message to $phone: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("Exception while sending WhatsApp message to $phone: " . $e->getMessage());
            return false;
        }
    }
}
