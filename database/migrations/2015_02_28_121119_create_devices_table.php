<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('devices', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('udid')->nullable();
            $table->string('user_id')->nullable();
            $table->string('device_type')->nullable();  // 0=unknow, 1=iOS, 2=Android, 3=OBD
            $table->string('device_token')->nullable();
            $table->integer('version')->nullable();
            $table->string('source')->nullable();   // 0=内测推广，1=apple商店，2=googlePlay
            $table->text('device_info')->nullable();
            $table->string('country_code')->nullable();

            $table->index('udid');

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
		Schema::drop('devices');
	}

}
