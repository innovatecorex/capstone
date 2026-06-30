<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GeoLocator — resolves an IP to a country using IPInfo Lite.
 * Fails safe: any error, timeout, or missing token returns null
 * and never interrupts the calling flow (e.g. login).
 */
class GeoLocator
{
    public static function describe(?string $ip): ?string
    {
        if (
            empty($ip)
            || $ip === '127.0.0.1'
            || $ip === '::1'
            || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
        ) {
            return null;
        }

        $token = config('services.ipinfo.token');
        if (empty($token)) {
            return null;
        }

        try {
            $response = Http::timeout(3)
                ->get("https://api.ipinfo.io/lite/{$ip}", ['token' => $token]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            $country   = $data['country']   ?? null;
            $continent = $data['continent'] ?? null;

            $parts = array_filter([$country, $continent]);

            return empty($parts) ? null : implode(', ', $parts);
        } catch (\Throwable $e) {
            Log::warning('GeoLocator lookup failed: ' . $e->getMessage());
            return null;
        }
    }
}
