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

        Schema::create('cart_item', function (Blueprint $table) {
            $table->id();
            $table->integer('cart_id');
            $table->integer('web_resource_id');
            $table->timestamps();

            $table->unique(['cart_id', 'web_resource_id']);
        });

        DB::commit();
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('cart_item');
        Schema::enableForeignKeyConstraints();
    }
};
