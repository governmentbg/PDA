<?php

namespace Feature;

use App\Enums\ArticleEnum;
use App\Enums\SettingEnum;
use App\Models\Article;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function published_article_can_be_viewed_in_list_of_articles()
    {
        $this->seed(SettingSeeder::class);
        $article = Article::factory()->create([
            'status' => ArticleEnum::STATUS_PUBLISHED,
        ]);

        $response = $this->get(route('article.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.article.index');
        $response->assertViewHas(
            'articles',
            Article::with(['image', 'type'])->where('status', ArticleEnum::STATUS_PUBLISHED)->orderBy(
                'id',
                'DESC'
            )->paginate(SettingEnum::getValueByKeyword(SettingEnum::SETTINGS_PAGINATION_LENGTH)),
        );
    }

    #[Test]
    function visitor_can_view_the_inner_part_of_an_article()
    {
        $article = Article::factory()->create([
            'status' => ArticleEnum::STATUS_PUBLISHED,
        ]);
        //setup

        //code
        $response = $this->get(route('article.view', ['id' => $article->id, 'slug' => $article->slug]));

        //assert
        $response->assertStatus(200);
        $response->assertViewIs('pages.article.article');
        $response->assertViewHas(
            'article',
            Article::with(['image', 'type'])->where(['status' => ArticleEnum::STATUS_PUBLISHED, 'id' => $article->id]
            )->first(),
        );


    }

}
