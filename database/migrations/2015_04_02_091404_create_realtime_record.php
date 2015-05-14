<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRealtimeRecord extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('web_realtimerecord', function(Blueprint $table)
        {
            $table->increments('id');

            $table->integer('user_id')->nullable();     // 上报的用户uid
            $table->string('device_id')->nullable();
            $table->string('jam_id')->nullable();
            $table->timestamp('st_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->boolean('ign')->nullable();      // 是否忽略这条记录

            // 真实坐标
            $table->integer('jam_start_lon')->nullable();
            $table->integer('jam_start_lat')->nullable();
            $table->integer('jam_end_lon')->nullable();
            $table->integer('jam_end_lat')->nullable();
            $table->integer('user_lon')->nullable();
            $table->integer('user_lat')->nullable();

            // baidu坐标
            $table->integer('jam_start_bdlon')->nullable();
            $table->integer('jam_start_bdlat')->nullable();
            $table->integer('jam_end_bdlon')->nullable();
            $table->integer('jam_end_bdlat')->nullable();
            $table->integer('user_bdlon')->nullable();
            $table->integer('user_bdlat')->nullable();

            $table->float('jam_duration')->nullable();
            $table->float('jam_speed')->nullable();
            $table->float('jam_dist')->nullable();
            $table->text('way_points')->nullable();     // 拥堵轨迹

            $table->timestamps();

            $table->index('user_id');
            $table->index('device_id');
            $table->index('jam_id');
            $table->index('st_date');
            $table->index('end_date');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('web_realtimerecord');
	}

}
