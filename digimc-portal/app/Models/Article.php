<?php

namespace App\Models;

use App\Enums\ArticleEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Article extends Model implements Feedable, Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use HasFactory;

    protected $table = "article";
    protected $fillable = [
        'published_at',
        'article_type_id',
        'title',
        'slug',
        'content',
        'status',
    ];
    public $timestamps = true;
    public $casts = [
        'published_at' => 'datetime',
        'article_type_id' => 'integer',
        'title' => 'string',
        'slug' => 'string',
        'content' => 'string',
        'status' => 'string',
    ];
    public static $rules = [
        'published_at' => 'nullable|date',
        'article_type_id' => 'required',
        'title' => 'required',
        'slug' => 'required',
        'content' => 'min:10',
    ];

    public function type(): HasOne
    {
        return $this->hasOne(ArticleType::class, 'id', 'article_type_id');
    }
    public function image():HasOne
    {
        return $this->hasOne(ArticleImage::class, 'article_id', 'id');
    }

    public static function getFeedItems()
    {
        return self::query()
            ->where('status', ArticleEnum::STATUS_PUBLISHED)
            ->latest('published_at')
            ->take(20)
            ->get();
    }

    public function toFeedItem(): FeedItem
    {
        $textContent = strip_tags($this->content);

        $excerpt = mb_substr($textContent, 0, 200) . '...';

        return FeedItem::create([
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $excerpt,
            'updated' => $this->updated_at,
            'link' => route('article.view', ['id' => $this->id, 'slug' => $this->slug]),
            'authorName' => '',
        ]);
    }
}
