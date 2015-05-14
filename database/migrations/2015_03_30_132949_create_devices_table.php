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
        Schema::create('web_devices', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('udid')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('device_type')->nullable();  // 0=unknow, 1=iOS, 2=Android, 3=OBD
            $table->string('device_token')->nullable();
            $table->integer('version')->nullable();
            $table->string('source')->nullable();   // 0=内测推广，1=apple商店，2=googlePlay
            $table->text('device_info')->nullable();
            $table->string('country_code')->nullable();

            $table->timestamps();

            $table->index('udid');
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
        Schema::drop('web_devices');
	}

}
