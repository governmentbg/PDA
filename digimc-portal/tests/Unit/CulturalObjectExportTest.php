<?php

namespace Tests\Unit;

use App\Models\CulturalObject;
use App\Models\WebResource;
use App\Services\CulturalObjectService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CulturalObjectExportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    private function makeObjectWithResources(array $objectAttrs = [], array $resources = []): CulturalObject
    {
        $object = CulturalObject::factory()->make($objectAttrs + [
                'title' => 'Test CulturalObject Title',
                'type' => 'text',
                'artist' => 'Test Artist',
                'description' => 'Test Description',
                'creation_date' => '2000-01-01',
                'current_location' => 'Test Location',
                'language_code' => 'EN',
                'previous_owner' => 'Test Previous Owner',
                'acquisition' => 'Test Acquisition',
                'rights_holder' => 'Test Right holder',
                'amount' => 123,
                'currency' => 'EUR',
            ]);

        $relations = collect();
        foreach ($resources as $r) {
            $relations->push(WebResource::factory()->make($r));
        }

        $object->setRelation('has_web_view_resource', $relations);

        return $object;
    }

    private function captureStreamedContent(StreamedResponse $response): string
    {
        ob_start();
        $response->sendContent();
        return (string)ob_get_clean();
    }

    #[Test]
    public function it_uses_head_content_length_when_available()
    {
        Config::set('app.url', 'https://test.example.com');

        Http::fake(function (Request $request) {
            if ($request->method() === 'HEAD') {
                return Http::response('', 200, ['Content-Length' => '66666']);
            }
            return Http::response('', 500);
        });

        $object = $this->makeObjectWithResources([], [
            [
                'id' => 10,
                'web_resource_address' => 'https://files.example.com/a.pdf',
                'format' => 'pdf', 'resource_type' => 'application/pdf'
            ]
        ]);

        $service = new CulturalObjectService();
        // setup

        // code
        $response = $service->streamCsv($object);
        $csv = $this->captureStreamedContent($response);

        // assert
        $this->assertStringContainsString("Cultural Object Metadata", $csv);
        $this->assertStringContainsString("Web Resource URL", $csv);
        $this->assertStringContainsString("https://files.example.com/a.pdf", $csv);
        $this->assertStringContainsString("File Size", $csv);
        $this->assertStringContainsString("KB", $csv);
        $this->assertSame('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    #[Test]
    public function it_falls_back_to_range_get_and_parses_content_range()
    {
        Http::fake(function (Request $request) {
            if ($request->method() === 'HEAD') {
                return Http::response('', 200, []);
            }

            if ($request->method() === 'GET') {
                $range = $request->header('Range');
                if (is_array($range)) {
                    $range = $range[0] ?? null;
                }

                if ($range === 'bytes=0-0') {
                    return Http::response('x', 206, ['Content-Range' => 'bytes 0-0/60000']);
                }
            }

            return Http::response('', 500);
        });

        $object = $this->makeObjectWithResources([], [
            [
                'id' => 11,
                'web_resource_address' => 'https://files.example.com/a.pdf'
            ]
        ]);

        $service = new CulturalObjectService();
        $response = $service->streamCsv($object);
        // setup

        // code
        $csv = $this->captureStreamedContent($response);

        // assert
        $this->assertStringContainsString("File Size", $csv);
        $this->assertStringContainsString("KB", $csv);
    }

    #[Test]
    public function it_handles_unknown_size_gracefully()
    {
        Http::fake(function (Request $request) {
            if ($request->method() === 'HEAD') {
                throw new ConnectionException("boom");
            }
            if ($request->method() === 'GET') {
                return Http::response('x', 200, []);
            }
            return Http::response('', 500);
        });

        $object = $this->makeObjectWithResources([], [
            [
                'id' => 12,
                'web_resource_address' => 'https://unknown.example.com/stream'
            ]
        ]);

        $service = new CulturalObjectService();
        $response = $service->streamCsv($object);
        // setup

        // code
        $csv = $this->captureStreamedContent($response);

        // assert
        $this->assertStringContainsString("unknown", $csv);
    }

    #[Test]
    public function it_honors_res_query_param_to_pick_specific_resource()
    {
        Http::fake(function (Request $request) {
            return Http::response('', 200, ['Content-Length' => '1000']);
        });

        $object = $this->makeObjectWithResources([], [
            ['id' => 101, 'web_resource_address' => 'https://files.example.com/first.pdf'],
            ['id' => 202, 'web_resource_address' => 'https://files.example.com/second.pdf'],
        ]);

        $this->app['request']->query->set('res', 202);

        $service = new CulturalObjectService();
        // setup

        // code
        $response = $service->streamCsv($object);
        $csv = $this->captureStreamedContent($response);

        // assert
        $this->assertStringContainsString("https://files.example.com/second.pdf", $csv);
        $this->assertStringNotContainsString("https://files.example.com/first.pdf", $csv);
    }

    #[Test]
    public function it_returns_404_when_no_resources()
    {
        $this->expectException(HttpException::class);

        $object = $this->makeObjectWithResources([], []);
        $service = new CulturalObjectService();
        // setup

        // code
        $service->streamCsv($object);
    }
}
