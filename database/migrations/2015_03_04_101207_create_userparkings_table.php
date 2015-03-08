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
		Schema::create('userparkings', function(Blueprint $table)
		{
			$table->increments('id');

            $table->string('pid')->nullable();      // 为了保证laravel插入正常，先插入数据，在填充数据，实际上不可为空
            $table->string('user_id')->nullable();
            $table->string('device_id')->nullable();
            $table->string('dna')->nullable();      // 用户上报数据的md5，为了防止重复
            $table->string('nearby_poi')->nullable();
            $table->string('user_mark')->nullable();
            $table->integer('rate')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('street')->nullable();
            $table->string('street_num')->nullable();
            $table->float('gps_lon')->nullable();
            $table->float('gps_lat')->nullable();
            $table->float('baidu_lon')->nullable();
            $table->float('baidu_lat')->nullable();
            $table->string('nav_id')->nullable();
            $table->string('circle_id')->nullable();

            $table->index('pid');
            $table->index('user_id');
            $table->index('device_id');

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('userparkings');
	}

}
