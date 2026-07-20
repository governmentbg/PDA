<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::beginTransaction();

        Schema::create('purchased_web_resource', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('web_resource_id');
            $table->integer('payment_id');

            $table->timestamp('purchased_at');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'web_resource_id']);
        });

        DB::commit();
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('purchased_web_resource');
        Schema::enableForeignKeyConstraints();
    }
};
