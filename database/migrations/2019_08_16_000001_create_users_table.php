<?php

use App\V1\Models\UserEmail;
use App\V1\Models\UserPhone;
use App\V1\Vendors\Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->rowFormat = 'DYNAMIC';

            $table->bigIncrements('id');
            $table->string('name');
            $table->string('display_name')->nullable();
            $table->string('password');
            $table->string('url_avatar');
            $table->timestamps();
            $table->softDeletes();

            $table->unique('name');
            $table->index('created_at');
            $table->index('deleted_at');
        });

        Schema::create('user_emails', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->rowFormat = 'DYNAMIC';

            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->tinyInteger('is_alias')->unsigned()->default(UserEmail::IS_ALIAS_YES);
            $table->string('email');
            $table->string('verified_code');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unique('email');
            $table->index('is_alias');
            $table->index('verified_at');
        });

        Schema::create('user_phones', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->rowFormat = 'DYNAMIC';

            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->tinyInteger('is_alias')->unsigned()->default(UserPhone::IS_ALIAS_YES);
            $table->integer('phone_code_id')->unsigned();
            $table->string('phone_number');
            $table->string('verified_code');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unique(['phone_code_id', 'phone_number']);
            $table->index('is_alias');
            $table->index('verified_at');
        });

        Schema::create('user_localizations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->rowFormat = 'DYNAMIC';

            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('locale')->default('en');
            $table->string('country')->default('US');
            $table->string('timezone')->default('UTC');
            $table->string('currency')->default('USD');
            $table->string('number_format')->default('point_comma');
            $table->tinyInteger('first_day_of_week')->unsigned()->default(0);
            $table->tinyInteger('long_date_format')->unsigned()->default(0);
            $table->tinyInteger('short_date_format')->unsigned()->default(0);
            $table->tinyInteger('long_time_format')->unsigned()->default(0);
            $table->tinyInteger('short_time_format')->unsigned()->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('user_socials', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->rowFormat = 'DYNAMIC';

            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('provider');
            $table->string('provider_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unique(['user_id', 'provider', 'provider_id']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_socials');
        Schema::dropIfExists('user_localizations');
        Schema::dropIfExists('user_phones');
        Schema::dropIfExists('user_emails');
        Schema::dropIfExists('users');
    }
}
