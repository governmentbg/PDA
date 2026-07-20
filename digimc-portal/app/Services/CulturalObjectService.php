<?php

namespace App\Services;

use App\Enums\CulturalObjectEnum;
use App\Models\CulturalObject;
use App\Models\WebResource;
use GuzzleHttp\Client;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CulturalObjectService
{
    /**
     * @param CulturalObject $object Fully loaded object (has_web_view_resource eager-loaded)
     * @return StreamedResponse
     */
    public function streamCsv(CulturalObject $object): StreamedResponse
    {
        $all = collect($object->has_web_view_resource ?? []);
        if ($all->isEmpty()) {
            abort(404, __('general.no_results_found'));
        }

        $requestedId = request()->query('res');

        if ($all->count() === 1 && empty($requestedId)) {
            $resource = $all->first();
        } elseif (!empty($requestedId)) {
            $resource = $all->firstWhere('id', (int)$requestedId);
            abort_if(!$resource, 404, __('general.no_results_found'));
        } else {
            $resource = $all->first();
        }

        // filename
        $baseName = Str::slug($resource->identifier ?? 'export') . '.csv';
        $filename  = $this->prependBrandName($baseName, 'text/csv');

        $callback = function () use ($object, $resource) {
            $out = fopen('php://output', 'w');
            $write = static fn(array $row) => fputcsv($out, $row); // default delimiter ","

            // Section 1: CulturalObject
            $write(['Cultural Object Metadata']);
            $write(['Title', $object->title ?? '']);
            $write(['Type', $object->type ?? '']);
            $write(['Artist', $object->artist ?? '']);
            $write(['Description', $object->description ?? '']);
            $write(['Creation Date', $this->asText($object->creation_date ?? '')]);
            $write(['Location', $object->current_location ?? '']);
            $write(['Language', $object->language_code ?? '']);
            $write(['Previous Owner', $object->previous_owner ?? '']);
            $write(['Acquisition', $object->acquisition ?? '']);
            $write(['Rights Holder', $object->rights_holder ?? '']);
            $write(['Amount', $this->asText($object->amount ?? '')]);
            $write(['Currency', $object->currency ?? '']);

            // spacer
            $write(['']);

            // Section 2: WebResource
            $write(['Web Resource Metadata']);
            $write(['Identifier', $resource->identifier ?? '']);
            $write(['Creator', $resource->creator ?? '']);
            $write(['Description', $resource->description ?? '']);
            $write(['Format', $resource->format ?? '']);
            $write(['Resource Type', $resource->resource_type ?? '']);
            $write(['Conforms To', $resource->conforms_to ?? '']);
            $write(['Created At', $this->asText($resource->created_at ?? '')]);
            $write(['Extent', $resource->extent ?? '']);
            $write(['Issued', $resource->issued ?? '']);
            $write(['Web Resource URL', $resource->web_resource_address ?? '']);
            $write(['Content Warning', $resource->content_warning ?? '']);
            $write(['Warning Text', $resource->warning_text ?? '']);
            $write(['Visualization Type', $resource->visualizationtype ?? '']);

            // size
            $bytes = $this->calcFileSize($resource->web_resource_address);
            $write(['File Size', $this->humanBytes($bytes)]);

            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * @param string $url Remote absolute URL (http/https).
     * @param array<string,string> $extraHeaders Extra headers (e.g., Authorization, Referer).
     * @phpstan-param non-empty-string $url
     * @phpstan-return positive-int|null
     * @return int|null Size in bytes (positive integer) or null if not available.
     */
    private function calcFileSize($url, $extraHeaders = []): ?int
    {
        $common = Http::withHeaders(array_merge([
            'User-Agent' => 'Exporter/1.0 (+https://example.com)',
            'Accept' => '*/*',
        ], $extraHeaders))
            ->timeout(8)
            ->connectTimeout(4)
            ->retry(1, 200);

        // 1) HEAD
        try {
            $head = $common->head($url);

            if ($head->successful()) {
                $length = $head->header('Content-Length');
                if (is_numeric($length)) {
                    return (int)$length;
                }
            }
        } catch (\Throwable $e) {
            // connection error
        }

        // 2) Range GET (1 byte)
        try {
            $get = $common
                ->withHeaders(['Range' => 'bytes=0-0'])
                ->get($url);

            $contentRange = $get->header('Content-Range');
            if ($get->status() === 206 && $contentRange) {
                if (preg_match('#^bytes\s+\d+-\d+/(\d+)$#i', $contentRange, $match)) {
                    return (int)$match[1];
                }
            }

            $length = $get->header('Content-Length');
            if ($get->successful() && is_numeric($length)) {
                return (int)$length;
            }
        } catch (\Throwable $e) {
            // connection error
        }

        return null;
    }

    /**
     * @param int|null $bytes Byte count, or null when unknown.
     * @phpstan-param positive-int|0|null $bytes
     * @return string Human readable size (e.g., "58.59 KB", "9.54 MB", or "unknown").
     */
    private function humanBytes($bytes): string
    {
        if ($bytes === null) {
            return 'unknown';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return number_format($bytes, $i ? 2 : 0) . ' ' . $units[$i];
    }

    /**
     * @param string|int|float|\Stringable|null $value Value to render as text.
     * @return string String-safe value; empty string if null.
     */
    private function asText($value): string
    {
        if ($value === null) return '';
        return "\u{200B}" . $value;
    }

    /**
     * @param CulturalObject $object
     * @return array{ resources: Collection<int, WebResource> }
     */
    public function makeCsvExportPayload(CulturalObject $object): array
    {
        $resources = collect($object->has_web_view_resource ?? []);

        $csvUrls = $resources->map(
            fn($r) => route('cultural_object.export', ['id' => $object->id, 'res' => $r->id])
        )->values()->all();

        return [
            'resources'  => $resources,
        ];
    }

    /**
     * @param CulturalObject $itemToDownload
     * @param int|string|null $resourceId
     * @return StreamedResponse
     *
     * @throws RuntimeException
     * @throws NotFoundHttpException|ConnectionException
     */
    public function downloadObject(CulturalObject $itemToDownload, int|string|null $resourceId): StreamedResponse
    {
        $resources = $itemToDownload->has_web_view_resource;
        if ($resources->isEmpty()) {
            throw new RuntimeException(__('general.no_results_found'));
        }

        // one or many resources
        /** @var WebResource|null $resource */
        $resource = $resourceId !== null
            ? $resources->firstWhere('id', $resourceId)
            : $resources->first();
        if (!$resource) {
            throw new RuntimeException(__('general.no_results_found'));
        }

        $url = $resource->web_resource_address;
        if (!$url || !Str::startsWith($url, ['http://', 'https://'])) {
            throw new RuntimeException('INVALID_RESOURCE_ADDRESS');
        }

        // stream request
        $remote = Http::withOptions([
            'stream' => true,
            'read_timeout' => 60,
            'timeout' => 300,
        ])->get($url);
        if ($remote->failed()) {
            throw new NotFoundHttpException('Remote file not available.');
        }

        // file name
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $filename = basename($path) ?: 'download';
        $contentType = $remote->header('Content-Type') ?: 'application/octet-stream';

        $filename = $this->prependBrandName($filename, $contentType);

        return response()->streamDownload(function () use ($remote) {
            $body = $remote->toPsrResponse()->getBody();
            while (!$body->eof()) {
                echo $body->read(8192);
                flush();
            }
        }, $filename, [
            'Content-Type' => $contentType,
        ]);
    }

    /**
     * @param string $filename
     * @param string $contentType
     * @return string
     */
    public function prependBrandName($filename, $contentType): string
    {
        $host = parse_url(config('app.url', ''), PHP_URL_HOST) ?: request()->getHost();
        $host = $host ? preg_replace('/^www\./i', '', $host) : 'website';

        $baseName = pathinfo($filename, PATHINFO_FILENAME) ?: 'download';
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $mimeOnly = strtolower(trim(strtok($contentType, ';')));

        // no extensions
        if ($extension === '') {
            $map = [
                'image/svg+xml' => 'svg',
                'image/png' => 'png',
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                'application/pdf' => 'pdf',
                'application/zip' => 'zip',
                'application/json' => 'json',
                'text/plain' => 'txt',
                'text/html' => 'html',
                'text/csv' => 'csv',
            ];
            $extension = $map[$mimeOnly] ?? '';
        }

        $prepended = $host . '-' . $baseName;
        return $extension ? ($prepended . '.' . $extension) : $prepended;
    }

    /**
     * Get IIIF info for TIFF page
     *
     * @param string $web_id
     * @param int $page_number
     * @return array
     * @throws \Exception
     */
    public function getTiffPageInfo(string $web_id, int $page_number): array
    {

        if ($page_number < 1) {
            throw new \Exception(__('cultural_object.errors.invalid_page_number'), 400);
        }

        $webResource = WebResource::find($web_id);
        if (!$webResource) {
            throw new \Exception(__('cultural_object.errors.the_web_resource_was_not_found'), 404);
        }

        if ($webResource->visualizationtype !== CulturalObjectEnum::TIFF) {
            throw new \Exception(__('cultural_object.errors.no_tiff_resource_found'), 404);
        }

        $iiifBase = config('services.iiif.base_url');
        $address = $this->normalizeResourceAddress($webResource->web_resource_address);
        $objectKeyWithPage = "{$address};page={$page_number}";
        $encodedKey = rawurlencode($objectKeyWithPage);
        $iiifUrl = "{$iiifBase}{$encodedKey}/info.json";

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json; charset=utf-8',
            ])
                ->timeout(30)
                ->withoutVerifying()
                ->get($iiifUrl);

            if ($response->failed()) {
                throw new \Exception('Failed to get IIIF info', $response->status());
            }

            $jsonBody = $response->json();
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return [
                    '_debug' => [
                        'web_id' => $web_id,
                        'page_number' => $page_number,
                        'original_url' => $webResource->web_resource_address,
                        'normalized_address' => $address,
                        'encoded_key' => $encodedKey,
                        'iiif_url' => $iiifUrl,
                        'exception_message' => $e->getMessage(),
                        'exception_code' => $e->getCode(),
                    ]
                ];
            }
            throw $e;
        }

        if (config('app.debug')) {
            $jsonBody['_debug'] = [
                'web_id' => $web_id,
                'page_number' => $page_number,
                'original_url' => $webResource->web_resource_address,
                'normalized_address' => $address,
                'encoded_key' => $encodedKey,
                'iiif_url' => $iiifUrl,
            ];
        }

        return $jsonBody;
    }

    public function transformIIIFJson(string $web_id, int $page_number, array $data): array
    {
        $iiifBase = config('services.iiif.base_url');

        if (isset($data['@id'])) {
            $fullIdentifier = str_replace($iiifBase, '', $data['@id']);

            $laravelProxyBase = route('cultural_object.proxy-tiff-tile', [
                'web_id' => $web_id,
                'page_number' => $page_number,
                'iiif_path' => 'IIIF_PLACEHOLDER'
            ]);
            $laravelProxyBase = str_replace('IIIF_PLACEHOLDER', '', $laravelProxyBase);

            $data['@id'] = $laravelProxyBase . $fullIdentifier;

            $data['@id'] = urldecode($data['@id']);
        }

        return $data;
    }


    public function proxyTileRequest(string $web_id, int $page_number, string $iiif_path)
    {
        $webResource = WebResource::find($web_id);
        if (!$webResource) {
            throw new \Exception('Resource not found', 404);
        }

        $iiifBase = config('services.iiif.base_url');


        $parts = explode('/full/', $iiif_path, 2);

        if (count($parts) < 2) {
            $cantaloupeIdWithPage = $iiif_path;
            $iiifCommand = '';
        } else {
            list($cantaloupeIdWithPage, $iiifCommand) = $parts;
        }

        $finalParts = explode(';', $cantaloupeIdWithPage, 2);

        if (count($finalParts) < 2) {
            throw new \Exception('Invalid IIIF identifier format (missing ;page=N)', 400);
        }

        list($cantaloupeFileId, $pageParam) = $finalParts;

        $fullyEncodedFileId = rawurlencode($cantaloupeFileId);

        $canonicalIdentifier = $fullyEncodedFileId . ";" . $pageParam;

        $internalTileUrl = !empty($iiifCommand)
            ? "{$iiifBase}{$canonicalIdentifier}/full/{$iiifCommand}"
            : "{$iiifBase}{$canonicalIdentifier}";

        try {
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->get($internalTileUrl);

            if ($response->failed()) {
                throw new \Exception('IIIF Server Error: ' . $response->status() . ' - URL: ' . $internalTileUrl, $response->status());
            }

            return response($response->body(), $response->status())
                ->header('Content-Type', $response->header('Content-Type'))
                ->header('Cache-Control', 'public, max-age=3600, must-revalidate');

        } catch (\Exception $e) {
            if (config('app.debug')) {
                return [
                    '_debug' => [
                        'web_id' => $web_id,
                        'page_number' => $page_number,
                        'iiif_path' => $iiif_path,
                        'internal_tile_url' => $internalTileUrl,
                        'exception_message' => $e->getMessage(),
                        'exception_code' => $e->getCode(),
                    ]
                ];
            }
            throw $e;
        }
    }

    function normalizeResourceAddress($address)
    {
        if (empty($address)) {
            throw new \Exception('Empty resource address');
        }

        if (str_starts_with($address, 'http://') || str_starts_with($address, 'https://')) {
            $parsed = parse_url($address);

            if ($parsed === false || !isset($parsed['path'])) {
                throw new \Exception('Invalid URL format: ' . $address);
            }

            $address = $parsed['path'];

            if (isset($parsed['query']) && !empty($parsed['query'])) {
                $address .= '?' . $parsed['query'];
            }
        }

        return ltrim($address, '/');
    }

    public function getSignedUrl(string $fileKey): ?array
    {

        $apiUrl = config('settings.signing_api_url');
        $period = config('settings.period_minutes');

        if (empty($apiUrl)) {
            \Log::error('Video signing API URL not configured.');
            return null;
        }

        try {

            $response = Http::timeout(5)
                ->acceptJson()
                ->post($apiUrl, [
                    'file_key' => $fileKey,
                    'period'   => $period,
                ]);

            if ($response->failed()) {
                \Log::error('Video signing failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();

            if (
                empty($data['url']) ||
                empty($data['valid_until'])
            ) {
                \Log::error('Invalid signing response structure', [
                    'response' => $data,
                ]);
                return null;
            }

            return [
                'url' => $data['url'],
                'valid_until' => $data['valid_until'],
            ];

        } catch (\Throwable $e) {

            \Log::error('Video signing exception', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
