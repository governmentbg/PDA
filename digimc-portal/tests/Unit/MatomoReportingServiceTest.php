<?php

namespace Tests\Unit;

use App\Services\MatomoReportingService;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tests\TestCase;

class MatomoReportingServiceTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(ClientInterface::class, function () {
            return new Client(['handler' => new MockHandler()]);
        });
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeResponse(string $body): ResponseInterface
    {
        $stream = Mockery::mock(StreamInterface::class);
        $stream->shouldReceive('__toString')->andReturn($body);

        $resp = Mockery::mock(ResponseInterface::class);
        $resp->shouldReceive('getBody')->andReturn($stream);

        return $resp;
    }

    public function test_get_counters_with_scalar_json()
    {
        $this->markTestSkipped('Temporarily skipped this test.');
        // Arrange
        $http = Mockery::mock(ClientInterface::class);
        $http->shouldReceive('post')->once()
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], '2'));
        $http->shouldReceive('post')->once()
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], '5'));

        $_ENV['MATOMO_URL'] = 'https://matomo.example.test/';
        $_ENV['MATOMO_SITE_ID'] = (string)random_int(1000, 9999);
        $_ENV['MATOMO_TOKEN'] = 'your-matomo-token-placeholder';
        $_ENV['MATOMO_COUNTER_CACHE_TTL'] = '30';

        $service = new MatomoReportingService($http);

        // Act
        $response = $service->getCounters();

        // Assert
        $this->assertSame(['today' => 2, 'total' => 5], $response);

    }

    public function test_get_counters_with_array_json()
    {
        $this->markTestSkipped('Temporarily skipped this test.');
        // Arrange
        $http = Mockery::mock(ClientInterface::class);
        $http->shouldReceive('post')->once()->andReturn(
            new Response(200, ['Content-Type' => 'application/json'], '{"2025-09-02":3}')
        );
        $http->shouldReceive('post')->once()->andReturn(
            new Response(200, ['Content-Type' => 'application/json'], '{"2025-09-01":7,"2025-09-02":3}')
        );

        $_ENV['MATOMO_URL'] = 'https://matomo.example.test/';
        $_ENV['MATOMO_SITE_ID'] = (string)random_int(1000, 9999);
        $_ENV['MATOMO_TOKEN'] = 'your-matomo-token-placeholder';
        $_ENV['MATOMO_COUNTER_CACHE_TTL'] = '30';

        $service = new MatomoReportingService($http);

        // Act
        $response = $service->getCounters();

        // Assert
        $this->assertSame(['today' => 3, 'total' => 10], $response);
    }

    public function test_graceful_failure_returns_zeroes()
    {
        // Arrange
        $http = Mockery::mock(Client::class);
        $http->shouldReceive('post')->twice()->andThrow(new \RuntimeException('boom'));

        $_ENV['MATOMO_URL'] = 'https://matomo.example.test/';
        $_ENV['MATOMO_SITE_ID'] = (string)random_int(1000, 9999);
        $_ENV['MATOMO_TOKEN'] = 'your-matomo-token-placeholder';
        $_ENV['MATOMO_COUNTER_CACHE_TTL'] = '30';

        $service = new MatomoReportingService($http);

        // Act
        $response = $service->getCounters();

        // Assert
        $this->assertSame(['today' => 0, 'total' => 0], $response);
    }

}
