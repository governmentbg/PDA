<?php

namespace App\Http\Controllers;

use App\Enums\ArticleEnum;
use App\Models\Article;
use App\Services\GalleryService;
use Illuminate\Http\Request;
use App\Models\CulturalObject;

class HomeController extends Controller
{
    public function index()
    {
        $service = new GalleryService();

        $culturalObjects = CulturalObject::with('provider')->inRandomOrder()->take(6)->get();
        $recentPublicCollections = $service->latestPublicCollections(6);
        $latestNews = Article::query()
            ->where('status', ArticleEnum::STATUS_PUBLISHED)
            ->with('image')
            ->orderByDesc('published_at')->take(3)->get();

        return view('welcome', compact('culturalObjects', 'recentPublicCollections', 'latestNews'));
    }
}
