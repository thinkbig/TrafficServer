<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model {

    protected $table = 'cars';
    protected $connection = 'cm_user';
    protected static $unguarded = true;

}