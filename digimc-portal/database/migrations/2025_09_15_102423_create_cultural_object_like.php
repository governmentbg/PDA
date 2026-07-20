<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        DB::beginTransaction();
        Schema::create('cultural_object_like', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('cultural_object_id')->index();
            $table->integer('user_id')->index();

            $table->timestamps();
            $table->softDeletes();

        });

        DB::commit();
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('cultural_object_like');
        Schema::enableForeignKeyConstraints();
    }
};
