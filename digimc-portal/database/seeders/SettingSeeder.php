<?php

namespace Database\Seeders;

use App\Enums\SettingEnum;
use App\Models\Setting;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table(with(new Setting())->getTable())->truncate();
        foreach (SettingEnum::getKeywordDefaultValue() as $keyword => $value) {
            $row = Setting::create(
                [
                    'keyword' => $keyword,
                    'value' => $value,
                ]
            );
        }

        Schema::enableForeignKeyConstraints();
    }
}
