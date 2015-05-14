<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('web_cars', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('cid')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('car_no')->nullable();
            $table->string('license_no')->nullable();
            $table->string('engine_no')->nullable();
            $table->string('car_company')->nullable();
            $table->string('car_brand')->nullable();
            $table->string('date_buy')->nullable();
            $table->text('car_info')->nullable();

            $table->timestamps();

            $table->index('cid');
            $table->index('user_id');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('web_cars');
	}

}
