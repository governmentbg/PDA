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
        Schema::create('article_type', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();

            $table->softDeletes();
        });

        Schema::create('article_image', function (Blueprint $table) {
            $table->id();

            $table->integer('article_id')->index();
            $table->tinyInteger('sort_weight')->default(0)->index();
            $table->string('filepath');
            $table->string('filename');
            $table->text('description')->nullable();


            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('article', function (Blueprint $table) {
            $table->id();
            $table->datetime('published_at')->nullable();
            $table->integer('article_type_id')->index();
            $table->string('title');
            $table->string('slug');
            $table->text('content')->nullable();
            $table->string('status')->index();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_type');
        Schema::dropIfExists('article_image');
        Schema::dropIfExists('article');
    }
};
