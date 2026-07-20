<?php

namespace Database\Seeders;

use App\Models\ArticleType;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ArticleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        $file = File::get(database_path('fixtures/ArticleType.json'));

        DB::table(with(new ArticleType())->getTable())->truncate();
        foreach (json_decode($file, true) as $val) {
            $row = ArticleType::create(
                [
                    'name' => $val['name'],
                ]
            );
        }

        Schema::enableForeignKeyConstraints();
    }
}
