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
            $table->timestamp('requested_at')->nullable()->after('status');
            $table->timestamp('published_at')->nullable()->after('requested_at');
            $table->text('rejection_reason')->nullable()->after('published_at');
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
            $table->dropColumn(['requested_at', 'published_at', 'rejection_reason']);
        });
        Schema::enableForeignKeyConstraints();
    }
};
