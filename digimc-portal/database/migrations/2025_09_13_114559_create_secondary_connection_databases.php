<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'secondary';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (env('APP_ENV') != "testing" && !env('RUN_SECONDARY_MIGRATION', false)) {
            return;
        }

        if(env('APP_ENV') == "testing"){

            //add testing sqlite migrations here

                Schema::create('provider', function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('title');
                    $table->string('identifier');
                    $table->text('description');
                    $table->string('type');
                    $table->string('phone_number');
                    $table->string('address');
                    $table->string('email');
                    $table->string('website');
                    $table->string('territory');
                    $table->string('contact_person');
                });

                Schema::create('cultural_object', function (Blueprint $table) {
                    $table->increments('id');

                    $table->string('identifier');
                    $table->string('type');
                    $table->string('title');
                    $table->string('original_title');
                    $table->string('other_title');
                    $table->string('artist');
                    $table->text('description');
                    $table->integer('cultural_object_provided_by');
                    $table->string('creation_date');
                    $table->string('current_location');
                    $table->string('keywords');
                    $table->string('theme');
                    $table->string('subject_heading');
                    $table->string('geographic_heading');
                    $table->string('temporal_heading');
                    $table->string('language_code');
                    $table->string('physical_dimensions');
                    $table->string('medium');
                    $table->string('previous_owner');
                    $table->string('acquisition');
                    $table->string('original_media');
                    $table->string('rights_holder');
                    $table->string('rights');
                    $table->text('contentdescription');
                    $table->decimal('amount', 10,2);
                    $table->string('currency');

                    $table->text('thumbnail_url');
                    $table->text('extended_view_url');
                });

                Schema::create('web_resource', function (Blueprint $table) {
                    $table->increments('id');

                    $table->string('identifier');
                    $table->string('type');
                    $table->string('creator');
                    $table->string('description');
                    $table->string('format');
                    $table->string('rights_holder');
                    $table->string('resource_type');
                    $table->string('conforms_to');
                    $table->date('created_at');
                    $table->time('extent');
                    $table->string('issued');
                    $table->string('web_resource_address');
                    $table->string('rights');
                    $table->string('sensitive_content');
                    $table->string('content_warning');
                    $table->string('warning_text');
                    $table->string('visualizationtype');
                    $table->decimal('price', 10,2);
                    $table->string('paid_content')->nullable();
                    $table->string('trailer_address')->nullable();
                    $table->string('web_resource_address_download')->nullable();
                    $table->string('mimetype_thumbnail')->nullable();
                    $table->string('mimetype_trailer')->nullable();
                    $table->string('mimetype_download')->nullable();
                    $table->string('source')->nullable();
                    $table->string('title')->nullable();
                });
                Schema::create('has_web_view', function (Blueprint $table) {

                    $table->integer('cultural_object_id');
                    $table->integer('web_resource_id');
                });


            //add testing sqlite migrations here
        } else {
            try {
                $sql = file_get_contents(database_path('fixtures/digimc.sql'));
                DB::connection($this->connection)->unprepared($sql);
            } catch (\Exception $exception) {
                dd($exception->getMessage(), $exception->getFile(), $exception->getLine());
            }

        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (env('APP_ENV') != "testing" && !env('RUN_SECONDARY_MIGRATION', false)) {
            return;
        }

        if(env('APP_ENV') == "testing"){
            Schema::dropIfExists('provider');
            Schema::dropIfExists('cultural_object');
            Schema::dropIfExists('web_resource');
        } else {

        }
    }
};
