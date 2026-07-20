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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('profile_image_path')->nullable()->after('password');

            $table->boolean('wants_notifications')->default(false)->after('profile_image_path');
            $table->boolean('subscribed_news')->default(false)->after('wants_notifications');
            $table->boolean('subscribed_weekly')->default(false)->after('subscribed_news');

            $table->string('activation_token')->nullable()->after('remember_token');
            $table->timestamp('activation_token_expires_at')->nullable()->after('activation_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'profile_image_path',
                'wants_notifications', 'subscribed_news', 'subscribed_weekly',
                'activation_token', 'activation_token_expires_at',
            ]);
        });
    }
};
