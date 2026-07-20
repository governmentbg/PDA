<?php

namespace Database\Seeders;

use App\Models\ArticleType;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(ArticleTypeSeeder::class);
        $this->call(PageSeeder::class);
        if(app()->environment() !== 'production') {
            $this->call(UserSeeder::class);
        }

    }
}
