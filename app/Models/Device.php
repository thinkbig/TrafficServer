<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model {

    protected $table = 'devices';
    protected $connection = 'cm_user';
    protected static $unguarded = true;

    // device type define
    const IOS = 1;
    const Android = 2;
    const OBD = 3;

    // source type
    const Internal = 1;
    const AppStore = 2;
    const GooleStore = 3;

}
