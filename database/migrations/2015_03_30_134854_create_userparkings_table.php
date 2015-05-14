<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserparkingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('back_userparkings', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('pid')->nullable();      // 为了保证laravel插入正常，先插入数据，在填充数据，实际上不可为空
            $table->integer('user_id')->nullable();
            $table->string('device_id')->nullable();
            $table->string('nearby_poi')->nullable();
            $table->string('user_mark')->nullable();
            $table->integer('rate')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('street')->nullable();
            $table->string('street_num')->nullable();
            $table->integer('gps_lon')->nullable();
            $table->integer('gps_lat')->nullable();
            $table->integer('baidu_lon')->nullable();
            $table->integer('baidu_lat')->nullable();
            $table->string('nav_id')->nullable();
            $table->string('circle_id')->nullable();

            $table->timestamps();

            $table->index('pid');
            $table->index('user_id');
            $table->index('device_id');
            $table->index('gps_lon');
            $table->index('gps_lat');
            $table->index('updated_at');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('back_userparkings');
	}

}
