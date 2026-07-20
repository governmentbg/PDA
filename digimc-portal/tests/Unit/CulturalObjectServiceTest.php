<?php

namespace Tests\Unit;

use App\Enums\CulturalObjectEnum;
use App\Models\CulturalObject;
use App\Models\WebResource;
use App\Services\CulturalObjectService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class CulturalObjectServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_downloads_first_resource_when_no_resource_id(): void
    {
        $object = CulturalObject::factory()->make(['cultural_object_provided_by' => null]);
        $res1 = WebResource::factory()->make([
            'id' => 11,
            'web_resource_address' => 'https://test.com/file1.jpg',
        ]);
        $res2 = WebResource::factory()->make([
            'id' => 22,
            'web_resource_address' => 'https://test.com/file2.jpg',
        ]);
        $object->setRelation('has_web_view_resource', collect([$res1, $res2]));

        Http::fake([
            'https://test.com/file1.jpg' => Http::response('IMG1', 200, ['Content-Type' => 'image/jpeg']),
        ]);
        $service = new CulturalObjectService();
        // setup

        // code
        $response = $service->downloadObject($object, null);
        ob_start();
        $response->sendContent();
        $body = ob_get_clean();

        // assert
        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertSame('image/jpeg', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.jpg', $response->headers->get('Content-Disposition'));

        $this->assertSame('IMG1', $body);
    }

    #[Test]
    public function it_downloads_specific_resource_when_res_id_provided(): void
    {
        $object = CulturalObject::factory()->make(['cultural_object_provided_by' => null]);
        $object->setAttribute('has_web_view_resource', collect([
            (object)['id' => 11, 'web_resource_address' => 'https://test.com/a.pdf'],
            (object)['id' => 22, 'web_resource_address' => 'https://test.com/b.pdf'],
        ]));

        Http::fake([
            'https://test.com/b.pdf' => Http::response('%PDF-B%', 200, ['Content-Type' => 'application/pdf']),
        ]);

        $service = new CulturalObjectService();
        // setup

        // code
        $response = $service->downloadObject($object, 22);
        ob_start();
        $response->sendContent();
        $body = ob_get_clean();

        // assert
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.pdf', $response->headers->get('Content-Disposition'));

        $this->assertSame('%PDF-B%', $body);
    }

    #[Test]
    public function it_prepends_host_from_app_url_and_strips_www(): void
    {
        Config::set('app.url', 'https://www.test.org/testing');
        $service = new CulturalObjectService();
        // setup

        // code
        $name = $service->prependBrandName('photo.png', 'image/png');

        // assert
        $this->assertStringStartsWith('test.org-photo.png', $name);
        $this->assertSame('test.org-photo.png', $name);
    }

    #[Test]
    public function it_prepend_brand_name_adds_extension_from_mime_if_missing(): void
    {
        $service = new CulturalObjectService();
        // setup

        // code
        $name = $service->prependBrandName('file', 'image/jpeg');

        // assert
        $this->assertStringEndsWith('.jpg', $name);
        $this->assertMatchesRegularExpression('/^[^\.]+-.+\.jpg$/', $name);
    }

    #[Test]
    public function prepend_brand_name_preserves_existing_extension(): void
    {
        $service = new CulturalObjectService();
        // setup

        // code
        $name = $service->prependBrandName('test.jpg', 'application/octet-stream');

        // assert
        $this->assertStringEndsWith('.jpg', $name);
    }

    #[Test]
    public function it_throws_when_no_resources(): void
    {
        // assert
        $this->expectException(\RuntimeException::class);

        $object = CulturalObject::factory()->make(['cultural_object_provided_by' => null]);
        $object->setAttribute('has_web_view_resource', collect([]));
        $service = new CulturalObjectService();
        // setup

        // code
        $service->downloadObject($object, null);
    }

    #[Test]
    public function it_throws_when_invalid_res_id(): void
    {
        // assert
        $this->expectException(\RuntimeException::class);

        $object = CulturalObject::factory()->make(['cultural_object_provided_by' => null]);
        $object->setAttribute('has_web_view_resource', collect([
            (object)['id' => 1, 'web_resource_address' => 'https://example.com/a.jpg'],
        ]));
        $service = new CulturalObjectService();
        // setup

        // code
        $service->downloadObject($object, 999);
    }

    #[Test]
    public function it_throws_on_invalid_url_scheme(): void
    {
        // assert
        $this->expectException(\RuntimeException::class);

        $object = CulturalObject::factory()->make(['cultural_object_provided_by' => null]);
        $object->setAttribute('has_web_view_resource', collect([
            (object)['id' => 1, 'web_resource_address' => 'ftp://bad.test.com/file'],
        ]));
        $service = new CulturalObjectService();
        // setup

        // code
        $service->downloadObject($object, 1);
    }

    #[Test]
    public function it_throws_when_remote_fails(): void
    {
        // assert
        $this->expectException(NotFoundHttpException::class);

        $object = CulturalObject::factory()->make(['cultural_object_provided_by' => null]);
        $object->setAttribute('has_web_view_resource', collect([
            (object)['id' => 1, 'web_resource_address' => 'https://test.com/missing.jpg'],
        ]));

        Http::fake([
            'https://test.com/missing.jpg' => Http::response('nope', 404),
        ]);
        $service = new CulturalObjectService();
        // setup

        // code
        $service->downloadObject($object, 1);
    }

    #[Test]
    public function it_returns_tiff_page_info_successfully(): void
    {
        config(['app.debug' => false]);
        $webResource = WebResource::factory()->create([
            'visualizationtype' => CulturalObjectEnum::TIFF,
            'web_resource_address' => 'test-image'
        ]);
        $webId = $webResource->id;
        $pageNumber = 1;

        $mockData = [
            'width' => 2000,
            'height' => 1500,
            'tiles' => [['width' => 256, 'height' => 256]]
        ];

        Http::fake([
            config('services.iiif.base_url') . 'test-image%3Bpage%3D1/info.json' => Http::response($mockData, 200)
        ]);

        $service = new CulturalObjectService();
        $result = $service->getTiffPageInfo($webId, $pageNumber);

        $this->assertEquals($mockData, $result);
    }

    #[Test]
    public function it_properly_encodes_special_characters_in_url(): void
    {
        config(['app.debug' => false]);
        $webResource = WebResource::factory()->create([
            'visualizationtype' => CulturalObjectEnum::TIFF,
            'web_resource_address' => 'test image with spaces'
        ]);
        $webId = $webResource->id;
        $pageNumber = 1;

        $mockData = ['width' => 1000, 'height' => 800];

        Http::fake([
            config('services.iiif.base_url') . 'test%20image%20with%20spaces%3Bpage%3D1/info.json' => Http::response($mockData, 200)
        ]);

        $service = new CulturalObjectService();
        $result = $service->getTiffPageInfo($webId, $pageNumber);

        $this->assertEquals($mockData, $result);
    }

    #[Test]
    public function it_handles_http_request_failure(): void
    {
        Config::set('app.debug', false);
        $webResource = WebResource::factory()->create([
            'visualizationtype' => CulturalObjectEnum::TIFF,
            'web_resource_address' => 'test-image'
        ]);
        $webId = $webResource->id;
        $pageNumber = 1;

        Http::fake([
            config('services.iiif.base_url') . 'test-image%3Bpage%3D1/info.json' => Http::response(null, 404)
        ]);

        $service = new CulturalObjectService();
        $this->expectException(\Exception::class);

        $service->getTiffPageInfo($webId, $pageNumber);
    }

    #[Test]
    public function it_throws_exception_for_invalid_page_number(): void
    {
        $webResource = WebResource::factory()->create();
        $webId = $webResource->id;
        $invalidPageNumber = 0;

        $service = new CulturalObjectService();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(__('cultural_object.errors.invalid_page_number'));
        $this->expectExceptionCode(400);

        $service->getTiffPageInfo($webId, $invalidPageNumber);
    }

    #[Test]
    public function it_throws_exception_when_web_resource_not_found(): void
    {
        $nonExistentWebId = 9999;
        $pageNumber = 1;

        $service = new CulturalObjectService();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(__('cultural_object.errors.the_web_resource_was_not_found'));
        $this->expectExceptionCode(404);

        $service->getTiffPageInfo($nonExistentWebId, $pageNumber);
    }

    #[Test]
    public function it_throws_exception_when_resource_is_not_tiff(): void
    {
        $webResource = WebResource::factory()->create([
            'visualizationtype' => 'pdf',
            'web_resource_address' => 'test-document'
        ]);
        $webId = $webResource->id;
        $pageNumber = 1;

        $service = new CulturalObjectService();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(__('cultural_object.errors.no_tiff_resource_found'));
        $this->expectExceptionCode(404);

        $service->getTiffPageInfo($webId, $pageNumber);
    }

    #[Test]
    public function it_rewrites_internal_iiif_id_to_public_proxy_url(): void
    {
        $webResource = WebResource::factory()->create();
        $service = new CulturalObjectService();

        $relativeIiifPath = 'image/test.ptif;page=1';

        $mockJson = [
            '@id' => config('services.iiif.base_url') . $relativeIiifPath,
            'width' => 2000,
            'height' => 1500,
        ];

        $result = $service->transformIIIFJson($webResource->id, 1, $mockJson);

        $expectedUrlStart = route('cultural_object.proxy-tiff-tile', [
            'web_id' => $webResource->id,
            'page_number' => 1,
            'iiif_path' => $relativeIiifPath,
        ]);

        $this->assertStringStartsWith($expectedUrlStart, $result['@id']);
    }

    #[Test]
    public function it_successfully_proxies_tile_request(): void
    {
        Http::preventStrayRequests();

        $webResource = WebResource::factory()->create([
            'visualizationtype' => CulturalObjectEnum::TIFF,
        ]);

        $mockImageBytes = 'fake-image-data';
        $iiifBase = config('services.iiif.base_url');
        $iiifPath = 'image/test-file.ptif;page=1/full/0,0,256,256/256,/0/default.jpg';
        $encodedFile = rawurlencode('image/test-file.ptif');

        $internalTileUrl = "{$iiifBase}{$encodedFile};page=1/full/0,0,256,256/256,/0/default.jpg";

        Http::fake([
            $internalTileUrl => Http::response($mockImageBytes, 200, [
                'Content-Type' => 'image/jpeg',
            ]),
        ]);

        $service = new CulturalObjectService();
        $response = $service->proxyTileRequest($webResource->id, 1, $iiifPath);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('image/jpeg', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('fake-image-data', $response->getContent());
    }

    #[Test]
    public function it_throws_exception_when_resource_not_found(): void
    {
        $service = new CulturalObjectService();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Resource not found');
        $this->expectExceptionCode(404);

        $service->proxyTileRequest(9999, 1, 'image/test.ptif;page=1/full/0,0,256,256/256,/0/default.jpg');
    }

    #[Test]
    public function it_throws_exception_on_invalid_identifier(): void
    {
        $webResource = WebResource::factory()->create([
            'visualizationtype' => CulturalObjectEnum::TIFF,
        ]);

        $service = new CulturalObjectService();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid IIIF identifier format (missing ;page=N)');
        $this->expectExceptionCode(400);

        $service->proxyTileRequest($webResource->id, 1, 'invalid-format-without-semicolon');
    }

    #[Test]
    public function it_throws_exception_when_iiif_server_returns_error(): void
    {
        Config::set('app.debug', false);
        Http::preventStrayRequests();

        $webResource = WebResource::factory()->create([
            'visualizationtype' => CulturalObjectEnum::TIFF,
        ]);

        $iiifBase = config('services.iiif.base_url');
        $iiifPath = 'image/test.ptif;page=1/full/0,0,256,256/256,/0/default.jpg';
        $encodedFile = rawurlencode('image/test.ptif');
        $internalTileUrl = "{$iiifBase}{$encodedFile};page=1/full/0,0,256,256/256,/0/default.jpg";

        Http::fake([
            $internalTileUrl => Http::response(null, 500),
        ]);

        $service = new CulturalObjectService();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/IIIF Server Error/');
        $this->expectExceptionCode(500);

        $service->proxyTileRequest($webResource->id, 1, $iiifPath);
    }

    #[Test]
    public function it_throws_exception_when_iiif_server_returns_error_debug_false(): void
    {
        $webResource = WebResource::factory()->create([
            'visualizationtype' => CulturalObjectEnum::TIFF,
            'web_resource_address' => 'test-image',
        ]);
        $webId = $webResource->id;
        $pageNumber = 1;

        Http::fake([
            config('services.iiif.base_url') . 'test-image%3Bpage%3D1/info.json' => Http::response(null, 404),
        ]);

        $service = new CulturalObjectService();

        config()->set('app.debug', false);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to get IIIF info');

        $service->getTiffPageInfo($webId, $pageNumber);
    }

    #[Test]
    public function it_returns_debug_array_when_iiif_server_returns_error_debug_true(): void
    {
        $webResource = WebResource::factory()->create([
            'visualizationtype' => CulturalObjectEnum::TIFF,
            'web_resource_address' => 'test-image',
        ]);
        $webId = $webResource->id;
        $pageNumber = 1;

        Http::fake([
            config('services.iiif.base_url') . 'test-image%3Bpage%3D1/info.json' => Http::response(null, 404),
        ]);

        $service = new CulturalObjectService();

        config()->set('app.debug', true);
        $result = $service->getTiffPageInfo($webId, $pageNumber);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('_debug', $result);
        $this->assertArrayHasKey('web_id', $result['_debug']);
        $this->assertArrayHasKey('page_number', $result['_debug']);
        $this->assertArrayHasKey('original_url', $result['_debug']);
        $this->assertArrayHasKey('exception_message', $result['_debug']);
    }
}
