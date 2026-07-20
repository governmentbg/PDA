<?php

namespace Database\Seeders;

use App\Models\Gallery;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        $file = File::get(app_path('../database/fixtures/User.json'));

        Gallery::query()->delete();
        DB::table(with(new User())->getTable())->truncate();
        DB::table(with(new RoleUser())->getTable())->truncate();

        foreach (json_decode($file, true) as $row) {
            $user = User::create(
                [
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'password' => bcrypt(env('ADMIN_PASSWORD')),
                ]
            );

            foreach ($row['roles'] as $role) {
                $user->addRole(Role::where('name', $role)->first());
            }
        }

        Schema::enableForeignKeyConstraints();
    }
}
