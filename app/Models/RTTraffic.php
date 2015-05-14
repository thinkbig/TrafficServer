<?php namespace App\Models;
/**
 * Created by PhpStorm.
 * User: TaoQi
 * Date: 4/1/15
 * Time: 9:35 PM
 */

use Illuminate\Database\Eloquent\Model;

class RTTraffic extends Model {

    protected $table = 'web_realtimerecord';
    protected $connection = 'cm_user';
    protected static $unguarded = true;

}