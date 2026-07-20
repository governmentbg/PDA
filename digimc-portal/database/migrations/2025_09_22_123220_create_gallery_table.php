<?php

use App\Enums\GalleryEnum;
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
        Schema::create('gallery', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('user_id')->index();
            $table->string('status')->default(GalleryEnum::STATUS_PRIVATE);
            $table->timestamps();
            $table->softDeletes();
        });
        DB::commit();
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('gallery');
        Schema::enableForeignKeyConstraints();
    }
};
