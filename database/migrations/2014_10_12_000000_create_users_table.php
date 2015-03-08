<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');

            $table->string('uid')->nullable();
            $table->string('latest_device')->nullable();
            $table->string('latest_car')->nullable();
			$table->string('name')->nullable();
			$table->string('email')->nullable();
            $table->string('phone')->nullable();
			$table->string('password')->nullable();
            $table->text('intro')->nullable();
            $table->integer('experience')->nullable();

            $table->index('uid');

			$table->rememberToken();
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
		Schema::drop('users');
	}

}
