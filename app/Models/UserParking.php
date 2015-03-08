<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserParking extends Model {

    protected $table = 'userparkings';
    protected $connection = 'cm_user';
    protected static $unguarded = true;

}
