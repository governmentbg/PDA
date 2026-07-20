<?php

namespace Tests\Unit;

use App\Mail\WeeklySummaryMail;
use App\Models\Article;
use App\Models\User;
use App\Services\ArticleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SendWeeklyNewsCommandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function command_runs_successfully()
    {
        $this->artisan('emails:weekly-news')
            ->expectsOutput('Стартира изпращането на седмичните новини...')
            ->expectsOutput('Готово!')
            ->assertExitCode(0);
    }

    #[Test]
    public function it_sends_to_subscribed_users_only_with_news_within_the_last_7_days()
    {
        Mail::fake();
        Carbon::setTestNow($now = now());

        // news within 7 days
        $in1 = Article::factory()->create(['status' => 'published', 'published_at' => $now->copy()->subDays(2)]);
        $in2 = Article::factory()->create(['status' => 'published', 'published_at' => $now->copy()->subDays(6)]);

        // news before 7 days
        Article::factory()->create(['status' => 'published', 'published_at' => $now->copy()->subDays(8)]);

        // recipients
        $yes1 = User::factory()->create(['subscribed_weekly' => true, 'email' => 'a@test.com']);
        $yes2 = User::factory()->create(['subscribed_weekly' => true, 'email' => 'b@test.com']);
        $notSubscribed = User::factory()->create(['subscribed_weekly' => false, 'email' => 'c@test.com']);

        // setup

        // code
        app(ArticleService::class)->sendWeeklyNews();

        // assert
        Mail::assertSent(WeeklySummaryMail::class, fn($m) => $m->hasTo($yes1->email));
        Mail::assertSent(WeeklySummaryMail::class, fn($m) => $m->hasTo($yes2->email));
        Mail::assertSent(WeeklySummaryMail::class, 2);
        Mail::assertNotSent(WeeklySummaryMail::class, fn($m) => $m->hasTo('c@test.com'));
    }

    #[Test]
    public function it_includes_articles_from_start_of_window_and_excludes_older()
    {
        Mail::fake();
        Carbon::setTestNow($now = now());

        // within the window
        Article::factory()->create([
            'status' => 'published',
            'published_at' => $now->copy()->subDays(7)->startOfDay(),
        ]);

        // not within the window
        Article::factory()->create([
            'status' => 'published',
            'published_at' => $now->copy()->subDays(7)->startOfDay()->subSecond(),
        ]);

        User::factory()->create(['subscribed_weekly' => true, 'email' => 'a@test.com']);
        // setup

        // code
        app(ArticleService::class)->sendWeeklyNews();

        // assert
        Mail::assertSent(WeeklySummaryMail::class, 1);
    }

    #[Test]
    public function it_sends_one_mail_per_subscribed_user_even_with_multiple_articles()
    {
        Mail::fake();
        Article::factory()->count(3)->create(['status' => 'published', 'published_at' => now()->subDays(2)]);

        $u1 = User::factory()->create(['subscribed_weekly' => true, 'email' => 'a@test.com']);
        $u2 = User::factory()->create(['subscribed_weekly' => true, 'email' => 'b@test.com']);
        // setup

        // code
        app(ArticleService::class)->sendWeeklyNews();

        // assert
        Mail::assertSent(WeeklySummaryMail::class, 2);
        Mail::assertSent(WeeklySummaryMail::class, fn($m) => $m->hasTo($u1->email));
        Mail::assertSent(WeeklySummaryMail::class, fn($m) => $m->hasTo($u2->email));
    }

    #[Test]
    public function weekly_summary_mail_has_expected_subject_and_articles_payload()
    {
        Mail::fake();
        $u = User::factory()->create(['subscribed_weekly' => true, 'email' => 'u@test.com']);
        $a = Article::factory()->create(['status' => 'published', 'published_at' => now()->subDays(2), 'title' => 'Alpha']);
        $b = Article::factory()->create(['status' => 'published', 'published_at' => now()->subDays(3), 'title' => 'Beta']);
        // setup

        // code
        app(ArticleService::class)->sendWeeklyNews();

        // assert
        Mail::assertSent(WeeklySummaryMail::class, function (WeeklySummaryMail $m) use ($u, $a, $b) {
            $m->build();
            $expected = 'Новини от седмицата в ' . config('app.name');

            return $m->hasTo($u->email)
                && $m->subject === $expected
                && $m->articles->pluck('id')->sort()->values()->all() === collect([$a->id, $b->id])->sort()->values()->all();
        });
    }

    #[Test]
    public function it_sends_nothing_when_no_subscribers()
    {
        Mail::fake();

        // one article within 7 days
        Article::factory()->create(['status' => 'published', 'published_at' => now()->subDay()]);

        User::factory()->count(3)->create([
            'subscribed_weekly' => false,
            'email' => fn() => fake()->unique()->safeEmail(),
        ]);
        // setup

        // code
        app(ArticleService::class)->sendWeeklyNews();

        // assert
        Mail::assertNothingSent();
    }

    #[Test]
    public function it_ignores_non_published_articles()
    {
        Mail::fake();

        Article::factory()->create(['status' => 'draft', 'published_at' => now()->subDay()]);
        Article::factory()->create(['status' => 'archived', 'published_at' => now()->subDay()]);
        Article::factory()->create(['status' => 'published', 'published_at' => now()->subDay()]);

        User::factory()->create(['subscribed_weekly' => true, 'email' => 'a@test.com']);
        // setup

        // code
        app(ArticleService::class)->sendWeeklyNews();

        // assert
        Mail::assertSent(WeeklySummaryMail::class, 1);
    }

    #[Test]
    public function it_sends_nothing_if_no_recent_published_articles()
    {
        Mail::fake();
        Article::factory()->create(['status' => 'published', 'published_at' => Carbon::now()->subDays(10)]);
        // setup

        // code
        app(ArticleService::class)->sendWeeklyNews();

        // assert
        Mail::assertNothingSent();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
