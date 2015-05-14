<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbroadsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('back_tbroads', function(Blueprint $table)
        {
            $table->increments('id');

            $table->bigInteger('road_id')->nullable();      // 为了保证laravel插入正常，先插入数据，在填充数据，实际上不可为空
            $table->string('road_name')->nullable();
            $table->bigInteger('city_id')->nullable();      // 就是city数据库名
            $table->string('city_name')->nullable();
            $table->string('start_name')->nullable();
            $table->string('end_name')->nullable();
            $table->string('dir', 16)->nullable();
            $table->mediumInteger('road_level')->nullable();    //1高速  2，高架、快速路  3，地面道路

            $table->string('coor_type', 32)->nullable();    // 表示什么坐标系
            $table->integer('start_lon')->nullable();   // 目前只用到百度坐标
            $table->integer('start_lat')->nullable();
            $table->integer('end_lon')->nullable();
            $table->integer('end_lat')->nullable();

            $table->mediumText('way_points')->nullable();   // 预留字段，暂时没用到

            $table->timestamps();

            $table->index('coor_type');
            $table->index('start_lon');
            $table->index('start_lat');
            $table->index('end_lon');
            $table->index('end_lat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('back_tbroads');
    }

}
