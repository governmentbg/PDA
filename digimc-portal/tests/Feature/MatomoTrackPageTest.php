<?php

namespace Feature;

use Illuminate\Support\Facades\Route;
use Mockery;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;

class MatomoTrackPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Mockery::mock('overload:MatomoTracker')->shouldIgnoreMissing();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // uses overload in its own PHP process, so MatomoTracker isn’t preloaded
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_tracks_page_view_for_html_get_requests()
    {
        // Arrange
        $_ENV['MATOMO_SITE_ID'] = '1';
        $_ENV['MATOMO_URL'] = 'https://matomo.example.test/';

        Route::get('/probe-matomo', fn () => response('<html>ok</html>', 200));

        // Act
        $response = $this->get('/probe-matomo', [
            'Accept' => 'text/html',
        ]);

        // Assert
        $response->assertOk();
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_does_not_track_when_request_is_not_html()
    {
        // Arrange
        Route::get('/probe-json', fn () => response()->json(['ok' => true]));

        // Act
        $response = $this->get('/probe-json', [
            'Accept' => 'application/json',
        ]);

        // Assert
        $response->assertOk();
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_respects_do_not_track_header()
    {
        // Arrange
        Route::get('/probe-dnt', fn () => response('<html>ok</html>', 200));

        // Act
        $response = $this->get('/probe-dnt', [
            'Accept' => 'text/html',
            'DNT'    => '1',
        ]);

        // Assert
        $response->assertOk();
    }

}
