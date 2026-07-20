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
        Schema::create('gallery_cultural_object', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('gallery_id');
            $table->bigInteger('cultural_object_id')->index();
            $table->timestamps();
            $table->softDeletes();
        });
        DB::commit();
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('gallery_cultural_object');
        Schema::enableForeignKeyConstraints();
    }
};
