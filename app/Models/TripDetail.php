<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripDetail extends Model {

    protected $table = 'tripdetails';
    protected $connection = 'cm_user';
    protected static $unguarded = true;

}
