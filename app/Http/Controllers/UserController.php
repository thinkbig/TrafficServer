<?php namespace App\Http\Controllers;

use Request;
use Response;
use App\Models\Device;
use App\Utils\ToolUtil;

class UserController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

    public function postWakeup()
    {
        $request = Request::instance();
        $jsonStr = ToolUtil::getContent($request);
        $jsonObj = json_decode($jsonStr);

        $udid = isset($jsonObj->udid) ? $jsonObj->udid : null;
        $verify_key = isset($jsonObj->verify_key) ? $jsonObj->verify_key : null;

        $calVeryKey = md5($udid . "&eof|w>/>4>4;B3");
        if ($verify_key != $calVeryKey) {
            return ToolUtil::makeResp(null, -1);
        }

        // updateOrCreate method will not update the updated_at column if nothing changed
        $newAttr = array(
            'udid'     => $udid,
            'user_id' => (isset($jsonObj->user_id) ? $jsonObj->user_id : null),
            'device_type'    => (isset($jsonObj->device_type) ? $jsonObj->device_type : 0),
            'device_token' => (isset($jsonObj->device_token) ? $jsonObj->device_token : null),
            'version' => (isset($jsonObj->version) ? $jsonObj->version : 0),
            'source'    => (isset($jsonObj->source) ? $jsonObj->source : 0),
            'device_info' => (isset($jsonObj->device_info) ? $jsonObj->device_info : null),
            'country_code' => (isset($jsonObj->country_code) ? $jsonObj->country_code : null)
        );
        $modelBuilder = Device::where(array('udid' => $udid));
        $model = $modelBuilder->first();
        if ($model) {
            $modelBuilder->update($newAttr);
        } else {
            Device::create($newAttr);
        }

        return ToolUtil::makeResp(null);
    }

}
