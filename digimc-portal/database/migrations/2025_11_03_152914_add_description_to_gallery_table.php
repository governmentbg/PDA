<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::beginTransaction();
        Schema::table('gallery', function (Blueprint $table) {
            $table->text('description')->nullable()->after('user_id');
        });
        DB::commit();
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('gallery', function (Blueprint $table) {
            $table->dropColumn('description');
        });
        Schema::enableForeignKeyConstraints();
    }
};
