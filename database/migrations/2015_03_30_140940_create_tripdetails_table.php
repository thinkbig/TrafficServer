<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripdetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('back_tripdetails', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('tid')->nullable();
            $table->mediumText('detail')->nullable();
            $table->longText('gps_raw')->nullable();

            $table->timestamps();

            $table->index('tid');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('back_tripdetails');
	}

}
