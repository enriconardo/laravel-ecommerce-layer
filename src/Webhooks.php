<?php

namespace EcommerceLayer;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Webhooks
{
    public static function call(string $eventType, $data = [])
    {
        $registeredWebhooks = config('ecommerce-layer.webhooks');

        foreach ($registeredWebhooks as $webhook) {
            $url = Arr::get($webhook, 'url');

            if (!$url) {
                continue;
            }

            $headers = Arr::get($webhook, 'headers', []);

            Http::withOptions(['synchronous' => false])
                ->withHeaders($headers)
                ->timeout(5)
                ->retry(2, 10000)
                ->post($url, [
                    'event' => $eventType,
                    'data' => $data
                ]);
            
            Log::debug("Called webhook: $url - Event: $eventType");
        }
    }
}
