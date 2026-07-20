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
        Schema::create('page', function (Blueprint $table) {
            $table->id();
            $table->string('title', 250);
            $table->string('sef_title', 250)->unique();
            $table->text('content')->nullable();
            $table->string('status')->index();
            $table->timestamps();

            $table->softDeletes();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('page');
        Schema::enableForeignKeyConstraints();
    }
};
