<?php

namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class MatomoReportingService
{
    private Client $http;
    private int $siteId;
    private string $token;
    private string $baseUrl;
    private int $ttl;

    public function __construct(?Client $http = null)
    {
        $this->baseUrl = rtrim((string) config('matomo.MATOMO_URL', ''), '/') . '/';
        $this->siteId = (int) config('matomo.MATOMO_SITE_ID', 1);
        $this->token = (string) config('matomo.MATOMO_TOKEN', '');
        $this->ttl = (int) config('matomo.MATOMO_COUNTER_CACHE_TTL', 30);

        $this->http = $http ?? new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => (float) config('matomo.MATOMO_TIMEOUT', 1.2),
            'connect_timeout' => (float) config('matomo.MATOMO_CONNECT_TIMEOUT', 0.4),
        ]);
    }

    /** @return array{today:int,total:int} */
    public function getCounters(): array
    {
        $key = "matomo:counters:site:{$this->siteId}";

        return Cache::remember($key, $this->ttl, function () {
            return [
                'today' => $this->getVisits('day', Carbon::now()->format('Y-m-d')),
                'total' => $this->getVisits('range', '2005-01-01,'.Carbon::now()->format('Y-m-d')),
            ];
        });
    }

    private function getVisits(string $period, string $date): int
    {
        try {
            $resp = $this->http->post('index.php', [
                'form_params' => [
                    'module' => 'API',
                    'method' => 'VisitsSummary.getVisits',
                    'idSite' => $this->siteId,
                    'period' => $period,
                    'date' => $date,
                    'format' => 'JSON',
                    'token_auth' => $this->token,
                ],
            ]);

            $json = json_decode((string) $resp->getBody()->getContents(), true);

            if (is_array($json) && isset($json['result']) && $json['result'] === 'error') {
                return 0;
            }

            return is_array($json)
                ? array_sum(array_map('intval', $json))
                : (int) $json;
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
