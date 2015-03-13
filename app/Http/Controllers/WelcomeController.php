<?php namespace App\Http\Controllers;

use Request;
use App\Utils\ToolUtil;

class WelcomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('welcome');
	}

    public function postGzip()
    {
        $request = Request::instance();
        $jsonGzip = $request->getContent();
        $jsonStr = gzdecode($jsonGzip);

        $rawLen = strlen($jsonGzip);
        $len = strlen($jsonStr);
        $encoding = $request->header('Content-Encoding');
        $type = $request->header('Content-Type');

        return ToolUtil::makeResp(["len1" => $rawLen, "len2" => $len, "encoding" => $encoding, "type" => $type]);
    }

}
