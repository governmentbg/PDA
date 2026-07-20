<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Article;
use App\Models\ArticleType;
use App\Enums\ArticleEnum;


class ArticleFeedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function get_feed_items_returns_only_published_articles_and_limits_to_20()
    {
        Article::factory()->count(5)->create(['status' => ArticleEnum::STATUS_DRAFT]);
        Article::factory()->count(25)->create(['status' => ArticleEnum::STATUS_PUBLISHED]);

        $feedItems = Article::getFeedItems();

        $this->assertCount(20, $feedItems);
        $this->assertTrue($feedItems->every(fn($item) => $item->status === ArticleEnum::STATUS_PUBLISHED));
    }

    #[Test]
    public function to_feed_item_returns_feed_item_with_correct_fields_and_clean_summary()
    {
        $articleType = ArticleType::factory()->create();
        $article = Article::factory()->create([
            'article_type_id' => $articleType->id,
            'title' => 'Test Article',
            'content' => '<p>Hello <strong>world</strong></p>',
            'status' => ArticleEnum::STATUS_PUBLISHED,
        ]);

        $feedItem = $article->toFeedItem();

        $this->assertEquals($article->id, $feedItem->id);
        $this->assertEquals($article->title, $feedItem->title);
        $this->assertStringContainsString('Hello world', $feedItem->summary);
        $this->assertStringNotContainsString('<p>', $feedItem->summary);
        $this->assertEquals(route('article.view', ['id' => $article->id, 'slug' => $article->slug]), $feedItem->link);
    }

    #[Test]
    public function feed_items_are_sorted_by_published_at_desc()
    {
        $old = Article::factory()->create([
            'status' => ArticleEnum::STATUS_PUBLISHED,
            'published_at' => now()->subDays(2)
        ]);
        $new = Article::factory()->create([
            'status' => ArticleEnum::STATUS_PUBLISHED,
            'published_at' => now()
        ]);

        $feedItems = Article::getFeedItems();

        $this->assertEquals($new->id, $feedItems->first()->id);
        $this->assertEquals($old->id, $feedItems->last()->id);
    }

    #[Test]
    public function feed_item_link_points_to_correct_route()
    {
        $article = Article::factory()->create([
            'status' => ArticleEnum::STATUS_PUBLISHED
        ]);

        $feedItem = $article->toFeedItem();

        $this->assertEquals(route('article.view', ['id' => $article->id, 'slug' => $article->slug]), $feedItem->link);
    }

    #[Test]
    public function feed_summary_contains_text_only()
    {
        $article = Article::factory()->create([
            'status' => ArticleEnum::STATUS_PUBLISHED,
            'content' => '<p>Hello <strong>World</strong></p>'
        ]);

        $feedItem = $article->toFeedItem();

        $this->assertStringContainsString('Hello World', $feedItem->summary);
        $this->assertStringNotContainsString('<p>', $feedItem->summary);
    }

    #[Test]
    public function feed_does_not_return_more_than_20_items_when_many_exist()
    {
        Article::factory()->count(30)->create([
            'status' => ArticleEnum::STATUS_PUBLISHED
        ]);

        $feedItems = Article::getFeedItems();

        $this->assertCount(20, $feedItems);
    }

    #[Test]
    public function feed_items_are_sorted_descending()
    {
        $articles = Article::factory()->count(3)->sequence(
            ['published_at' => now()->subDays(3)],
            ['published_at' => now()->subDay()],
            ['published_at' => now()]
        )->create(['status' => ArticleEnum::STATUS_PUBLISHED]);

        $feedItems = Article::getFeedItems();

        $expectedOrder = $articles->sortByDesc('published_at')->pluck('id')->toArray();
        $actualOrder = $feedItems->pluck('id')->toArray();

        $this->assertEquals($expectedOrder, $actualOrder);
    }
}
