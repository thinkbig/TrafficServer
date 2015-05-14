<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeyrouteToTripsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('back_trips', function($table)
        {
            $table->mediumText('key_route');

            $table->index('st_parkingId');
            $table->index('ed_parkingId');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('back_trips', function($table)
        {
            $table->dropColumn('key_route');

            $table->dropIndex('st_parkingId');
            $table->dropIndex('ed_parkingId');
        });
	}

}
