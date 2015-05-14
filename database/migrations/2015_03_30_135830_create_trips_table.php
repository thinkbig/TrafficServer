<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('back_trips', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('tid')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('device_id')->nullable();
            $table->timestamp('st_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('st_parkingId')->nullable();
            $table->string('ed_parkingId')->nullable();
            $table->float('total_dist')->nullable();
            $table->float('total_during')->nullable();
            $table->float('max_speed')->nullable();
            $table->float('avg_speed')->nullable();
            $table->float('traffic_jam_dist')->nullable();
            $table->float('traffic_jam_during')->nullable();
            $table->float('traffic_avg_speed')->nullable();
            $table->integer('traffic_light_tol_cnt')->nullable();
            $table->integer('traffic_light_jam_cnt')->nullable();
            $table->float('traffic_light_waiting')->nullable();
            $table->integer('traffic_heavy_jam_cnt')->nullable();
            $table->float('traffic_jam_max_during')->nullable();

            $table->timestamps();

            $table->index('tid');
            $table->index('user_id');
            $table->index('device_id');
            $table->index('st_date');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('back_trips');
	}

}
