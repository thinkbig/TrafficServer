<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserParking extends Model {

    protected $table = 'back_userparkings';
    protected $connection = 'cm_user';
    protected static $unguarded = true;

}
