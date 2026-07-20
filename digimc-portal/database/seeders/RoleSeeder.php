<?php

namespace Database\Seeders;

use App\Models\Role;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Schema;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        $file = File::get(app_path('../database/fixtures/role.json'));

        DB::table(with(new Role())->getTable())->truncate();
        foreach (json_decode($file, true) as $row) {
            $row = Role::create(
                [
                    'name' => $row['name'],
                    'display_name' => $row['display_name'],
                    'description' => $row['description'],
                ]
            );
        }

        Schema::enableForeignKeyConstraints();
    }
}
