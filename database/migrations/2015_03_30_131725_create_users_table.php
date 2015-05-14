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
        Schema::create('web_users', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('user_string')->nullable();      // 保留字段，userstring，将来如果需要string id
            $table->string('latest_device')->nullable();
            $table->string('latest_car')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('password')->nullable();
            $table->text('intro')->nullable();
            $table->integer('experience')->nullable();

            $table->rememberToken();
            $table->timestamps();

            $table->index('user_string');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('web_users');
	}

}
