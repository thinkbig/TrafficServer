<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model {

    protected $table = 'back_trips';
    protected $connection = 'cm_user';
    protected static $unguarded = true;

}
