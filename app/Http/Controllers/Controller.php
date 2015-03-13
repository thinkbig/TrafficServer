<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Config;

abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;

//    public function __construct()
//    {
//        Config::set('session.driver', 'array');
//    }

}
