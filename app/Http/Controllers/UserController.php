<?php namespace App\Http\Controllers;

use Request;
use Response;
use App\Models\Device;
use App\Utils\ToolUtil;
use App\Utils\ErrUtil;

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

        $calVeryKey = md5($udid . "&101111102124119624762526252596651");
        if ($verify_key != $calVeryKey) {
            return ErrUtil::errResp(ErrUtil::err_authorize);
        }

        $deviceObj = Device::firstOrNew(['udid' => $udid]);
        $deviceObj->user_id = (isset($jsonObj->user_id) ? $jsonObj->user_id : null);
        $deviceObj->device_type = (isset($jsonObj->device_type) ? $jsonObj->device_type : 0);
        $deviceObj->device_token = (isset($jsonObj->device_token) ? $jsonObj->device_token : null);
        $deviceObj->version = (isset($jsonObj->version) ? $jsonObj->version : 0);
        $deviceObj->source = (isset($jsonObj->source) ? $jsonObj->source : 0);
        $deviceObj->device_info = (isset($jsonObj->device_info) ? $jsonObj->device_info : null);
        $deviceObj->country_code = (isset($jsonObj->country_code) ? $jsonObj->country_code : null);
        $deviceObj->touch();        // 即使数据没有变化，也要更新最近active的时间

        if ($deviceObj->save()) {
            return ToolUtil::makeResp(null);
        }
        return ErrUtil::errResp(ErrUtil::err_general);
    }

}
