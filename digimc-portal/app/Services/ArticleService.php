<?php

namespace App\Services;

use App\Enums\ArticleEnum;
use App\Enums\ArticleTypeEnum;
use App\Enums\SettingEnum;
use App\Mail\ArticlePublishedMail;
use App\Mail\NewArticlePublishedMail;
use App\Mail\WeeklySummaryMail;
use App\Models\Article;
use App\Models\ArticleImage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mail;

class ArticleService
{
    public function store(Request $request): Article
    {
        $input = $request->all();

        $input['status'] = ArticleEnum::STATUS_DRAFT;
        $article = Article::create($input);
        if($request->has('image')){
            $filename = sha1($request->image->getFilename());
            $file = $request->image->move(public_path('uploads'), $filename.'.'.$request->image->getClientOriginalExtension());
            $filepath = preg_split('{public\/}', $file);

            ArticleImage::create([
                'article_id' => $article->id,
                'filename' => $filename,
                'sort_weight' => 0,
                'filepath' => isset($filepath[1]) ? $filepath[1] : $file

            ]);
        }
        return $article;
    }

    public function update($id, Request $request): Article
    {
        /** @var Article|null $article */
        $article = Article::find($id);

        if ($article === null) {
            throw new \Exception('Новината не е намерена');
        }

        $article->fill(['status' => $article->status]);
        $article->fill($request->all());

        $article->save();

        if($request->has('image')){

            $filename = sha1($request->image->getFilename());
            $file = $request->image->move(public_path('uploads'), $filename.'.'.$request->image->getClientOriginalExtension());
            $filepath = preg_split('{public\/}', $file);

            $oldArticleImage = ArticleImage::where('article_id', $article->id)->first();
            if (!is_null($oldArticleImage)) {
                if (\Storage::exists($oldArticleImage->filepath)) {
                    \Storage::delete($oldArticleImage->filepath);
                }

                $oldArticleImage->delete();
            }

            ArticleImage::create([
                'article_id' => $article->id,
                'filename' => $filename,
                'sort_weight' => 0,
                'filepath' => isset($filepath[1]) ? $filepath[1] : $file

            ]);
        }


        return $article;
    }

    public function delete($id)
    {
        /** @var Article|null $article */
        $article = Article::find($id);

        if ($article === null) {
            throw new \Exception('Новината не е намерена');
        }

        $article->delete();
    }

    public function togglePublish($id)
    {
        /** @var Article|null $article */
        $article = Article::find($id);

        if ($article === null) {
            throw new \Exception('Новината не е намерена');
        }

        $wasDraft = $article->status == ArticleEnum::STATUS_DRAFT;

        if ($wasDraft) {
            $article->published_at = Carbon::now();
            $article->status = ArticleEnum::STATUS_PUBLISHED;
        } else {
            $article->published_at = null;
            $article->status = ArticleEnum::STATUS_DRAFT;
        }

        $article->save();

        return $article;
    }

    public function deleteImage($articleId, $imageId)
    {
        /** @var ArticleImage|null $image */
        $image = ArticleImage::where('article_id', $articleId)->where('id', $imageId)->first();

        if(!is_null($image) && \Storage::exists($image->filepath))
        {
            \Storage::delete($image->filepath);
        }
        $image->delete();
    }

    public function sendDailyNews(bool $dryRun = false)
    {
        $appTz = config('app.timezone', 'Europe/Sofia');
        $yesterday = Carbon::yesterday($appTz);
        $startLocal = $yesterday->copy()->startOfDay();
        $endLocal = $yesterday->copy()->endOfDay();

        $articles = Article::where('status', ArticleEnum::STATUS_PUBLISHED)
            ->where('article_type_id', ArticleTypeEnum::NEWS)
            ->whereBetween('published_at', [$startLocal, $endLocal])
            ->orderBy('published_at')
            ->get();

        if ($articles->isEmpty()) {
            return [];
        }

        $recipientQuery = User::query()
            ->where('subscribed_news', true)
            ->whereNotNull('email');

        $recipientCount = $recipientQuery->count();
        if ($recipientCount === 0) {
            return "There are {$articles->count()} articles, but no subscribed users.";
        }

        if ($dryRun) {
            return "DRY RUN — articles: {$articles->count()}, recipients: {$recipientCount}";
        }

        $sent = 0;
        $recipientQuery->chunkById(500, function ($users) use ($articles, &$sent) {
            foreach ($users as $user) {
                Mail::to($user->email)
                    ->locale(app()->getLocale())
                    ->queue(new ArticlePublishedMail($user, $articles));
                $sent++;
            }
        });

        return "Daily news processed; articles: {$articles->count()}, recipients: {$sent}, dry-run: no";
    }

    public function sendWeeklyNews(): void
    {
        $from = Carbon::now()->subDays(7)->startOfDay();
        $to = Carbon::now()->endOfDay();


        $articles = Article::where('status', 'published')
            ->whereBetween('published_at', [$from, $to])
            ->get();

        if ($articles->isEmpty()) {
            info('Weekly news: няма нови статии за последната седмица.');
            return;
        }

        $users = User::where('subscribed_weekly', true)->get();

        foreach ($users as $user) {
            if ($user->email) {
                Mail::to($user->email)->send(new WeeklySummaryMail($user, $articles));
            }
        }

        info('Weekly news: седмичните имейли са изпратени успешно.');
    }
}
