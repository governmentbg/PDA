<?php

namespace Tests\Unit;

use App\Enums\PageEnum;
use App\Models\Page;
use App\Models\User;
use App\Services\PageService;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Support\Str;

class PageServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    function administrator_can_add_page()
    {
        //setup
        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);

        $title = "Sample Page Title";
        $content = fake()->paragraph();
        $request = Request::create('', '', [
            'title' => $title,
            'sef_title' => Str::slug($title, '-'),
            'content' => $content,
            'status' => PageEnum::STATUS_DRAFT,
        ]);

        //code
        $service = new PageService();
        $page = $service->store($request);

        //assert
        $this->assertSame($title, $page->title);
        $this->assertSame(Str::slug($title, '-'), $page->sef_title);
        $this->assertSame($content, $page->content);
        $this->assertSame(PageEnum::STATUS_DRAFT, $page->status);
    }

    #[Test]
    function administrator_can_update_only_content_of_page()
    {
        //setup
        $page = Page::factory()->create([
            'title' => 'Old Title',
            'sef_title' => 'old-title',
            'content' => 'Old content',
            'status' => PageEnum::STATUS_DRAFT,
        ]);

        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);

        $newContent = 'Updated content via Summernote';
        $request = Request::create('', '', [
            'title' => $page->title,
            'sef_title' => $page->sef_title,
            'content' => $newContent,
            'status' => $page->status,
        ]);

        //code
        $service = new PageService();
        $service->update($page->id, $request);

        //assert
        $page = $page->fresh();
        $this->assertSame($newContent, $page->content);
        $this->assertSame('Old Title', $page->title);
        $this->assertSame('old-title', $page->sef_title);
    }

    #[Test]
    function administrator_can_delete_page()
    {
        //setup
        $page = Page::factory()->create();
        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);
        $this->assertSame(1, Page::count());

        //code
        $service = new PageService();
        $service->delete($page->id);

        //assert
        $this->assertSame(0, Page::count());
        $this->assertSame(1, Page::withTrashed()->count());
    }

    #[Test]
    function administrator_can_toggle_publish_status()
    {
        //setup
        $page = Page::factory()->create([
            'status' => PageEnum::STATUS_DRAFT,
        ]);
        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);

        //code
        $service = new PageService();
        $service->togglePublish($page->id);

        //assert
        $page = $page->fresh();
        $this->assertSame(PageEnum::STATUS_PUBLISHED, $page->status);

        //code
        $service->togglePublish($page->id);

        //assert
        $page = $page->fresh();
        $this->assertSame(PageEnum::STATUS_DRAFT, $page->status);
    }

    #[Test]
    function administrator_can_update_sef_title_manually()
    {
        //setup
        $page = Page::factory()->create([
            'title' => 'Old Title',
            'sef_title' => 'old-title',
        ]);
        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);

        $newSef = 'custom-sef-title';
        $request = Request::create('', '', [
            'title' => $page->title,
            'sef_title' => $newSef,
            'content' => $page->content,
            'status' => $page->status,
        ]);

        //code
        $service = new PageService();
        $service->update($page->id, $request);

        //assert
        $page = $page->fresh();
        $this->assertSame($newSef, $page->sef_title);
    }

    #[Test]
    function administrator_can_save_html_content_from_summernote()
    {
        //setup
        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);

        $htmlContent = '<h1>Heading</h1><p>Paragraph <strong>bold</strong></p>';
        $request = Request::create('', '', [
            'title' => 'HTML Page',
            'sef_title' => 'html-page',
            'content' => $htmlContent,
            'status' => PageEnum::STATUS_DRAFT,
        ]);

        //code
        $service = new PageService();
        $page = $service->store($request);

        //assert
        $this->assertSame($htmlContent, $page->content);
    }

    #[Test]
    function sef_title_must_be_unique_on_creation()
    {
        //setup
        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);

        $page1 = Page::factory()->create(['sef_title' => 'unique-sef']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        $request = Request::create('', '', [
            'title' => 'Another Page',
            'sef_title' => 'unique-sef',
            'content' => 'Content here',
            'status' => PageEnum::STATUS_DRAFT,
        ]);

        //code
        $service = new PageService();
        $service->store($request);

        //assert
    }

    #[Test]
    function administrator_can_update_status_to_published_and_back()
    {
        //setup
        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);
        $page = Page::factory()->create(['status' => PageEnum::STATUS_DRAFT]);

        //code
        $service = new PageService();
        $service->togglePublish($page->id);

        //assert
        $page = $page->fresh();
        $this->assertSame(PageEnum::STATUS_PUBLISHED, $page->status);

        //code
        $service->togglePublish($page->id);

        //assert
        $page = $page->fresh();
        $this->assertSame(PageEnum::STATUS_DRAFT, $page->status);
    }

    #[Test]
    function administrator_can_store_and_update_long_content()
    {
        //setup
        $this->seed(RoleSeeder::class);
        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);

        $longContent = str_repeat('<p>Paragraph</p>', 1000);
        $request = Request::create('', '', [
            'title' => 'Long Content Page',
            'sef_title' => 'long-content-page',
            'content' => $longContent,
            'status' => PageEnum::STATUS_DRAFT,
        ]);

        //code
        $service = new PageService();
        $page = $service->store($request);

        //assert
        $this->assertSame($longContent, $page->content);

        //setup update
        $updatedContent = '<h2>Updated</h2>' . $longContent;
        $requestUpdate = Request::create('', '', [
            'title' => 'Long Content Page',
            'sef_title' => 'long-content-page',
            'content' => $updatedContent,
            'status' => PageEnum::STATUS_PUBLISHED,
        ]);

        //code
        $service->update($page->id, $requestUpdate);

        //assert
        $page = $page->fresh();
        $this->assertSame($updatedContent, $page->content);
        $this->assertSame(PageEnum::STATUS_PUBLISHED, $page->status);
    }

    #[Test]
    function administrator_can_add_image_in_summernote_content()
    {
        //setup
        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);

        $imageFile = \Illuminate\Http\UploadedFile::fake()->image('summernote-image.jpg');
        $imagePath = '/uploads/' . $imageFile->hashName();

        $htmlContent = '<p>Some text</p><p><img src="'.$imagePath.'" alt="Summernote Image"></p>';

        $request = Request::create('', '', [
            'title' => 'Page with Image',
            'sef_title' => Str::slug('Page with Image', '-'),
            'content' => $htmlContent,
            'status' => PageEnum::STATUS_DRAFT,
        ]);

        //code
        $service = new PageService();
        $page = $service->store($request);

        //assert
        $this->assertStringContainsString('<img src="'.$imagePath.'"', $page->content);
        $this->assertSame('Page with Image', $page->title);
        $this->assertSame(PageEnum::STATUS_DRAFT, $page->status);
    }

    #[Test]
    function update_nonexistent_page_throws_exception()
    {
        //setup
        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);

        $request = Request::create('', '', [
            'title' => 'Nonexistent',
            'sef_title' => 'nonexistent',
            'content' => 'Content',
            'status' => PageEnum::STATUS_DRAFT,
        ]);

        $service = new PageService();

        //assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Страницата не е намерена');

        //code
        $service->update(999, $request);
    }

    #[Test]
    function delete_nonexistent_page_throws_exception()
    {
        //setup
        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);

        $service = new PageService();

        //assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Страницата не е намерена');

        //code
        $service->delete(999);
    }

    #[Test]
    function toggle_publish_on_nonexistent_page_throws_exception()
    {
        //setup
        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);

        $service = new PageService();

        //assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Страницата не е намерена');

        //code
        $service->togglePublish(999);
    }
}

