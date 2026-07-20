<?php

namespace Database\Seeders;

use App\Enums\PageEnum;
use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table((new Page())->getTable())->truncate();

        Page::create([
            'sef_title' => 'za-nas',
            'title' => 'За нас',
            'content' => '<p>Информация за нас!</p>',
            'status' => PageEnum::STATUS_PUBLISHED,
        ]);

        Page::create([
            'sef_title' => 'about-us',
            'title' => 'About us',
            'content' => '<p>Information about us!</p>',
            'status' => PageEnum::STATUS_PUBLISHED,
        ]);

        Page::create([
            'sef_title' => 'rakovodstvo-za-upotreba',
            'title' => 'Ръководство за употреба',
            'content' => '<p>Помощна информация за употребата на портала.</p>',
            'status' => PageEnum::STATUS_PUBLISHED,
        ]);

        Page::create([
            'sef_title' => 'user-guide',
            'title' => 'User guide',
            'content' => '<p>Helpful information about using the portal.</p>',
            'status' => PageEnum::STATUS_PUBLISHED,
        ]);

        Page::create([
            'sef_title' => 'faq',
            'title' => 'Често задавани въпроси',
            'content' => '<h3>Как да се свържа с вас?</h3> <p>Можете да ни пишете или да се обадите на посочените контакти.</p>',
            'status' => PageEnum::STATUS_PUBLISHED,
        ]);


        Page::create([
            'sef_title' => 'faq-en',
            'title' => 'FAQ - Frequently Asked Questions',
            'content' => '<h3>How can I contact you?</h3> <p>You can write to us or call us at the contacts provided.</p>',
            'status' => PageEnum::STATUS_PUBLISHED,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
