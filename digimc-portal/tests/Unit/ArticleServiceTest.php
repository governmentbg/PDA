<?php

namespace Tests\Unit;

use App\Enums\ArticleEnum;
use App\Mail\ArticlePublishedMail;
use App\Mail\NewArticlePublishedMail;
use App\Mail\WeeklySummaryMail;
use App\Models\Article;
use App\Models\ArticleImage;
use App\Models\ArticleType;
use App\Services\ArticleService;
use Carbon\Carbon;
use Database\Seeders\ArticleTypeSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleServiceTest extends TestCase
{

    use RefreshDatabase;

    protected string $tz = 'Europe/Sofia';

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.timezone' => $this->tz]);
        date_default_timezone_set($this->tz);
    }

    #[Test]
    function administrator_can_add_article()
    {
        Carbon::setTestNow('2025-09-07 16:00:00');

        $this->seed(RoleSeeder::class);
        $this->seed(ArticleTypeSeeder::class);


        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);

        $title = "Dummy article title";
        $content = fake()->paragraph();
        $request = Request::create('', '', [
            'article_type_id' => ArticleType::first()->id,
            'title' => $title,
            'slug' => \Str::slug($title, '-'),
            'content' => $content,
        ]);
        $this->assertSame(0, Article::withTrashed()->count());
        //setup

        //code
        $articleService = new ArticleService();
        $article = $articleService->store($request);

        //assert
        $this->assertSame(ArticleType::first()->id, $article->article_type_id);
        $this->assertSame(ArticleEnum::STATUS_DRAFT, $article->status);
        $this->assertNull($article->published_at);
        $this->assertSame($title, $article->title);
        $this->assertSame(\Str::slug($title, '-'), $article->slug);
        $this->assertSame($content, $article->content);
    }

    #[Test]
    function administrator_can_delete_article()
    {
        $article = Article::factory()->create();
        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);
        $this->assertSame(1, Article::count());
        //setup

        //code
        $articleService = new ArticleService();
        $articleService->delete($article->id);
        //assert

        $this->assertSame(1, Article::withTrashed()->count());
        $this->assertSame(0, Article::count());
    }

    #[Test]
    function administrator_can_update_article()
    {
        $article = Article::factory()->create([
            'title' => 'Dummy article title',
        ]);
        $new_title = "New title";
        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);
        $request = Request::create('', '', [
            'article_type_id' => $article->article_type_id,
            'title' => $new_title,
            'slug' => \Str::slug($new_title, '-'),
            'content' => $article->content,
        ]);
        //setup

        //code
        $articleService = new ArticleService();
        $articleService->update($article->id, $request);

        //assert
        $article = $article->fresh();
        $this->assertSame($new_title, $article->title);
        $this->assertSame(\Str::slug($new_title,'-'), $article->slug);
    }

    #[Test]
    function administrator_can_publish_article()
    {
        Carbon::setTestNow('2025-09-08 15:30:00');
        $article = Article::factory()->create([
            'published_at' => null,
            'status' => ArticleEnum::STATUS_DRAFT,
        ]);
        $user = User::factory([
            'subscribed_news' => true,
        ])->withRole('administrator')->create();
        \Auth::login($user);
        Mail::fake();
        //setup

        //code
        $articleService = new ArticleService();
        $articleService->togglePublish($article->id);
        //assert
        $article = $article->fresh();
        $this->assertSame('2025-09-08 15:30:00', $article->published_at->format('Y-m-d H:i:s'));
        $this->assertSame(ArticleEnum::STATUS_PUBLISHED, $article->status);
        Mail::assertNothingSent();
    }

    #[Test]
    function administrator_can_unpublish_article()
    {
        Carbon::setTestNow('2025-09-08 15:30:00');
        $article = Article::factory()->create([
            'published_at' => '2025-09-08 15:30:00',
            'status' => ArticleEnum::STATUS_PUBLISHED,
        ]);
        $user = User::factory([
            'subscribed_news' => true,
        ])->withRole('administrator')->create();
        \Auth::login($user);
        Mail::fake();
        //setup

        //code
        $articleService = new ArticleService();
        $articleService->togglePublish($article->id);
        //assert
        $article = $article->fresh();
        $this->assertNull($article->published_at);
        $this->assertSame(ArticleEnum::STATUS_DRAFT, $article->status);
        Mail::assertNothingSent();
    }

    #[Test]
    function administrator_can_add_article_with_image()
    {
        Carbon::setTestNow('2025-09-07 16:00:00');

        $this->seed(RoleSeeder::class);
        $this->seed(ArticleTypeSeeder::class);


        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);
        $file = UploadedFile::fake()->image('random.jpg');
        $title = "Dummy article title";
        $content = fake()->paragraph();
        $request = Request::create('', '', [
            'article_type_id' => ArticleType::first()->id,
            'title' => $title,
            'slug' => \Str::slug($title, '-'),
            'content' => $content,
            'image' => $file,
        ]);
        $this->assertSame(0, Article::withTrashed()->count());
        //setup

        //code
        $articleService = new ArticleService();
        $article = $articleService->store($request);
        $article->fresh()->load('image');

        //assert
        $this->assertNotEmpty($article->image);
        $this->assertSame(ArticleType::first()->id, $article->article_type_id);
        $this->assertSame(ArticleEnum::STATUS_DRAFT, $article->status);
        $this->assertNull($article->published_at);
        $this->assertSame($title, $article->title);
        $this->assertSame(\Str::slug($title, '-'), $article->slug);
        $this->assertSame($content, $article->content);
    }

    #[Test]
    function administrator_can_upload_new_image_for_article_which_replaces_the_old_one()
    {
        $article = Article::factory()->create();
        $oldArticleImage = ArticleImage::factory()->create([
            'article_id' => $article->id,
            'filename' => 'old_image.jpg',
        ]);
        $this->assertSame(1, ArticleImage::count());
        $file = UploadedFile::fake()->image('random.jpg');

        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);

        $request = Request::create('', '', [
            'article_type_id' => $article->article_type_id,
            'title' => $article->title,
            'slug' => $article->slug,
            'content' => $article->content,
            'image' => $file,
        ]);
        //setup

        //code
        $articleService = new ArticleService();
        $articleService->update($article->id, $request);

        //assert
        $article = $article->fresh()->load('image');

        $this->assertSame(1, ArticleImage::onlyTrashed()->count());
        $this->assertSame(1, ArticleImage::count());
        $this->assertNotSame($oldArticleImage->id, $article->image->id);
        $this->assertNotSame('old_image.jpg', $article->image->filename);
    }

    #[Test]
    function administrator_can_delete_an_image_without_uploading_a_new_file()
    {
        $article = Article::factory()->create();
        $oldArticleImage = ArticleImage::factory()->create([
            'article_id' => $article->id,
            'filename' => 'old_image.jpg',
        ]);

        $file = UploadedFile::fake()->image('random.jpg');

        $user = User::factory()->withRole('administrator')->create();
        \Auth::login($user);
        //setup

        //code
        $articleService = new ArticleService();
        $articleService->deleteImage($article->id, $oldArticleImage->id);


        //assert
        $article = $article->fresh()->load('image');

        $this->assertSame(1, ArticleImage::onlyTrashed()->count());
        $this->assertSame(0, ArticleImage::count());
        $this->assertEmpty($article->image);
    }

    #[Test]
    function it_does_not_send_email_if_no_articles()
    {
        Carbon::setTestNow('2025-10-30 12:00:00');

        $user = User::factory([
            'subscribed_weekly' => true,
        ])->create();

        Mail::fake();

        //setup
        $articleService = new ArticleService();

        //code
        $articleService->sendWeeklyNews();

        //assert
        Mail::assertNothingSent();
    }

    #[Test]
    function it_sends_email_to_subscribed_users_if_articles_exist()
    {
        Carbon::setTestNow('2025-10-30 12:00:00');

        $article = Article::factory()->create([
            'status' => 'published',
            'published_at' => Carbon::now()->subDays(3),
        ]);

        $user1 = User::factory([
            'subscribed_weekly' => true,
            'email' => 'user1@example.com',
        ])->create();

        $user2 = User::factory([
            'subscribed_weekly' => false,
            'email' => 'user2@example.com',
        ])->create();

        Mail::fake();

        //setup
        $articleService = new ArticleService();

        //code
        $articleService->sendWeeklyNews();

        //assert
        Mail::assertSent(WeeklySummaryMail::class, function ($mail) use ($user1) {
            return $mail->hasTo($user1->email);
        });

        Mail::assertNotSent(WeeklySummaryMail::class, function ($mail) use ($user2) {
            return $mail->hasTo($user2->email);
        });

    }

    #[Test]
    function it_does_not_send_email_for_old_articles()
    {
        Carbon::setTestNow('2025-10-30 12:00:00');

        $article = Article::factory()->create([
            'status' => 'published',
            'published_at' => Carbon::now()->subDays(8),
        ]);

        $user = User::factory([
            'subscribed_weekly' => true,
            'email' => 'user@example.com',
        ])->create();

        Mail::fake();

        $articleService = new ArticleService();
        $articleService->sendWeeklyNews();

        Mail::assertNothingSent();
    }

    #[Test]
    function it_sends_email_with_all_articles_from_last_week()
    {
        Carbon::setTestNow('2025-10-30 12:00:00');

        $articles = Article::factory()->count(3)->create([
            'status' => 'published',
            'published_at' => Carbon::now()->subDays(2),
        ]);

        $user = User::factory([
            'subscribed_weekly' => true,
            'email' => 'user@example.com',
        ])->create();

        Mail::fake();

        $articleService = new ArticleService();
        $articleService->sendWeeklyNews();

        Mail::assertSent(WeeklySummaryMail::class, function ($mail) use ($user, $articles) {
            return $mail->hasTo($user->email) && $mail->articles->count() === $articles->count();
        });
    }

    #[Test]
    function it_does_not_send_email_if_no_subscribed_users()
    {
        Carbon::setTestNow('2025-10-30 12:00:00');

        $article = Article::factory()->create([
            'status' => 'published',
            'published_at' => Carbon::now()->subDays(1),
        ]);

        $user = User::factory([
            'subscribed_weekly' => false,
            'email' => 'user@example.com',
        ])->create();

        Mail::fake();

        $articleService = new ArticleService();
        $articleService->sendWeeklyNews();

        Mail::assertNothingSent();
    }

 #[Test]
    public function it_sends_one_mail_per_subscribed_user()
    {
        Mail::fake();
        Carbon::setTestNow('2025-10-30 12:00:00');
        $yesterday = Carbon::yesterday()->copy()->setTime(11, 00);

        Article::factory()->create([
            'status' => ArticleEnum::STATUS_PUBLISHED,
            'article_type_id' => 2,
            'published_at' => $yesterday,
            'title' => 'A',
            'slug' => 'a',
        ]);
        Article::factory()->create([
            'status' => ArticleEnum::STATUS_PUBLISHED,
            'article_type_id' => 2,
            'published_at' => $yesterday->copy()->addHour(),
            'title' => 'B',
            'slug' => 'b',
        ]);

        $user1 = User::factory()->create(['email' => 'user1@test.com', 'subscribed_news' => true]);
        $user2 = User::factory()->create(['email' => 'user2@test.com', 'subscribed_news' => true]);
        $user3 = User::factory()->create(['email' => 'nope@test.com', 'subscribed_news' => false]);
        //setup

        //code
        $articleService = new ArticleService();
        $message = $articleService->sendDailyNews();

        //assert
        $this->assertIsString($message);
        $this->assertStringContainsString('recipients: 2', $message);
        Mail::assertQueued(ArticlePublishedMail::class, 2);
        Mail::assertQueued(ArticlePublishedMail::class, function ($m) use ($user1) {
            return $m->user->is($user1) && $m->articles->count() === 2;
        });
    }

    #[Test]
    public function it_filters_by_article_type_and_status()
    {
        Mail::fake();
        $yesterday = Carbon::yesterday($this->tz)->copy()->setTime(11, 00);
        // correct type and status
        Article::factory()->create([
            'status' => ArticleEnum::STATUS_PUBLISHED,
            'article_type_id' => 2,
            'published_at' => $yesterday,
            'title' => 'Valid',
            'slug' => 'valid',
        ]);
        // wrong type
        Article::factory()->create([
            'status' => ArticleEnum::STATUS_PUBLISHED,
            'article_type_id' => 3,
            'published_at' => $yesterday,
            'title' => 'Wrong type',
            'slug' => 'wrong-type',
        ]);
        // wrong status
        Article::factory()->create([
            'status' => ArticleEnum::STATUS_DRAFT,
            'article_type_id' => 2,
            'published_at' => $yesterday,
            'title' => 'Draft',
            'slug' => 'draft',
        ]);

        User::factory()->create(['email' => 'sub@example.com', 'subscribed_news' => true]);
        //setup

        //code
        $articleService = new ArticleService();
        $articleService->sendDailyNews();

        //assert
        Mail::assertQueued(ArticlePublishedMail::class, 1);
        Mail::assertQueued(ArticlePublishedMail::class, function ($m) {
            return $m->articles->count() === 1 && $m->articles->first()->title === 'Valid';
        });
    }

    #[Test]
    public function it_report_counts_without_sending_with_dry_run()
    {
        Mail::fake();
        $yesterday = Carbon::yesterday($this->tz)->copy()->setTime(11, 00);

        Article::factory()->count(2)->create([
            'status' => ArticleEnum::STATUS_PUBLISHED,
            'article_type_id' => 2,
            'published_at' => $yesterday,
            'slug' => 'news',
            'title' => 'News',
        ]);

        User::factory()->create([
            'email' => 'test@test.com',
            'subscribed_news' => true,
        ]);
        //setup

        //code
        $articleService = new ArticleService();
        $message = $articleService->sendDailyNews(true);

        //assert
        $this->assertIsString($message);
        $this->assertStringContainsString('DRY RUN', $message);
        $this->assertStringContainsString('articles: 2', $message);
        $this->assertStringContainsString('recipients: 1', $message);

        Mail::assertNothingQueued();
    }

    #[Test]
    public function it_returns_message_when_no_recipients()
    {
        Mail::fake();
        $yesterday = Carbon::yesterday($this->tz)->copy()->setTime(11, 00);
        Article::factory()->create([
            'status' => ArticleEnum::STATUS_PUBLISHED,
            'article_type_id' => 2,
            'published_at' => $yesterday,
            'slug' => 'test-slug',
            'title' => 'Test News',
        ]);
        //setup

        //code
        $articleService = new ArticleService();
        $message = $articleService->sendDailyNews();

        //assert
        $this->assertIsString($message);
        $this->assertStringContainsString('but no subscribed users', $message);
        Mail::assertNothingQueued();
    }

    #[Test]
    public function it_returns_empty_array_when_no_articles()
    {
        Mail::fake();
        //setup

        //code
        $articleService = new ArticleService();
        $message = $articleService->sendDailyNews();

        //assert
        $this->assertIsArray($message);
        $this->assertSame([], $message);
        Mail::assertNothingQueued();
    }
}
