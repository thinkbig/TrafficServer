<?php namespace App\Http\Controllers;

use Request;
use App\Utils\ToolUtil;
use App\Utils\ErrUtil;
use App\Models\UserParking;

class ParkingController extends Controller {

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

    public function postDetail()
    {
        $uid = Request::input('uid');
        $udid = Request::input('udid');
        $pid = Request::input('pid');
        if (null == $udid) {
            return ErrUtil::errResp(ErrUtil::err_bad_parameters);
        }

        $request = Request::instance();
        $jsonStr = ToolUtil::getContent($request);
        $jsonObj = json_decode($jsonStr);

        if (isset($jsonObj->gps_lat) && isset($jsonObj->gps_lon))
        {
            if (strlen($pid) > 8) {
                $parkObj = UserParking::firstByAttributes(['pid' => $pid]);
            }
            if (!isset($parkObj) || null == $parkObj) {
                $parkObj = UserParking::firstOrNew(['user_id' => $uid, 'device_id' => $udid, 'gps_lon' => $jsonObj->gps_lon, 'gps_lat' => $jsonObj->gps_lat]);
            }

            if (strlen($parkObj->pid) < 8) {
                $force = Request::input('force');
                if (isset($force) && (1 == $force) && strlen($pid) > 8) {
                    $parkObj->pid = $pid;
                } else {
                    $parkObj->pid = ToolUtil::geneUUID();
                }
            }
            $pid = $parkObj->pid;

            $parkObj->user_id = $uid;
            $parkObj->device_id = $udid;
            $parkObj->nearby_poi = (isset($jsonObj->nearby_poi) ? $jsonObj->nearby_poi : null);
            $parkObj->user_mark = (isset($jsonObj->user_mark) ? $jsonObj->user_mark : null);
            $parkObj->rate = 0;
            $parkObj->province = (isset($jsonObj->province) ? $jsonObj->province : null);
            $parkObj->city = (isset($jsonObj->city) ? $jsonObj->city : null);
            $parkObj->district = (isset($jsonObj->district) ? $jsonObj->district : null);
            $parkObj->street = (isset($jsonObj->street) ? $jsonObj->street : null);
            $parkObj->street_num = (isset($jsonObj->street_num) ? $jsonObj->street_num : null);
            $parkObj->gps_lon = $jsonObj->gps_lon * 100000;
            $parkObj->gps_lat = $jsonObj->gps_lat * 100000;
            $parkObj->nav_id = null;
            $parkObj->circle_id = null;

            if ($parkObj->save()) {
                $result = ["pid" => $pid];
                return ToolUtil::makeResp($result, 0);
            }
        }

        return ErrUtil::errResp(ErrUtil::err_general);
    }

}
