<?php namespace App\Http\Controllers;

use Request;
use App\Utils\ToolUtil;
use App\Utils\ErrUtil;
use App\Utils\GeoTransform;
use App\Models\Trip;
use App\Models\TripDetail;
use App\Models\RTTraffic;
use App\Utils\BaiduHelper;

class TripController extends Controller {

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
        $tid = Request::input('tid');
        if (null == $udid) {
            return ErrUtil::errResp(ErrUtil::err_bad_parameters);
        }

        $request = Request::instance();
        $jsonStr = ToolUtil::getContent($request);
        $jsonObj = json_decode($jsonStr);

        if (isset($jsonObj->start_date) && isset($jsonObj->end_date) && isset($jsonObj->st_parkingId) && isset($jsonObj->ed_parkingId))
        {
            $stDate = ToolUtil::toDateTime($jsonObj->start_date);
            if (isset($tid)) {
                $tripObj = Trip::firstByAttributes(['tid' => $tid]);
            }
            if (!isset($tripObj) || null == $tripObj) {
                $tripObj = Trip::firstOrNew(['user_id' => $uid, 'device_id' => $udid, 'st_date' => $stDate]);
            }

            if (strlen($tripObj->tid) < 8) {
                $force = Request::input('force');
                if (isset($force) && (1 == $force) && strlen($tid) > 8) {
                    $tripObj->tid = $tid;
                } else {
                    $tripObj->tid = ToolUtil::geneUUID();
                }
            }
            $tid = $tripObj->tid;

            $tripObj->user_id = $uid;
            $tripObj->device_id = $udid;
            $tripObj->st_date = $stDate;
            $tripObj->end_date = ToolUtil::toDateTime($jsonObj->end_date);
            $tripObj->st_parkingId = $jsonObj->st_parkingId;
            $tripObj->ed_parkingId = $jsonObj->ed_parkingId;
            $tripObj->quality = (isset($jsonObj->quality) ? $jsonObj->quality : 0);
            $tripObj->total_dist = (isset($jsonObj->total_dist) ? $jsonObj->total_dist : 0);
            $tripObj->total_during = (isset($jsonObj->total_during) ? $jsonObj->total_during : 0);
            $tripObj->max_speed = (isset($jsonObj->max_speed) ? $jsonObj->max_speed : 0);
            $tripObj->avg_speed = (isset($jsonObj->avg_speed) ? $jsonObj->avg_speed : 0);
            $tripObj->traffic_jam_dist = (isset($jsonObj->traffic_jam_dist) ? $jsonObj->traffic_jam_dist : 0);
            $tripObj->traffic_jam_during = (isset($jsonObj->traffic_jam_during) ? $jsonObj->traffic_jam_during : 0);
            $tripObj->traffic_avg_speed = (isset($jsonObj->traffic_avg_speed) ? $jsonObj->traffic_avg_speed : 0);
            $tripObj->traffic_light_tol_cnt = (isset($jsonObj->traffic_light_tol_cnt) ? $jsonObj->traffic_light_tol_cnt : 0);
            $tripObj->traffic_light_jam_cnt = (isset($jsonObj->traffic_light_jam_cnt) ? $jsonObj->traffic_light_jam_cnt : 0);
            $tripObj->traffic_light_waiting = (isset($jsonObj->traffic_light_waiting) ? $jsonObj->traffic_light_waiting : 0);
            $tripObj->traffic_heavy_jam_cnt = (isset($jsonObj->traffic_heavy_jam_cnt) ? $jsonObj->traffic_heavy_jam_cnt : 0);
            $tripObj->traffic_jam_max_during = (isset($jsonObj->traffic_jam_max_during) ? $jsonObj->traffic_jam_max_during : 0);
            if (isset($jsonObj->addi_info) && $jsonObj->addi_info) {
                $tripObj->key_route = $jsonObj->addi_info;
            }

            if ($tripObj->save()) {
                $tripDetailObj = TripDetail::firstOrNew(['tid' => $tid]);
                $tripDetailObj->detail = $jsonStr;

                if ($tripDetailObj->save()) {
                    $hasRaw = (isset($tripDetailObj->gps_raw) && strlen($tripDetailObj->gps_raw)>8);
                    $result = ["tid" => $tid, "has_raw" => $hasRaw];
                    return ToolUtil::makeResp($result, 0);
                }
            }
        }

        return ErrUtil::errResp(ErrUtil::err_general);
    }


    public function postRaw()
    {
        // 这里将来要加验证，否则上报就没有限制，很容易被脏数据攻击
        $tid = Request::input('tid');
        $request = Request::instance();
        $jsonStr = ToolUtil::getContent($request);
        if (null == $tid || strlen($jsonStr) < 10) {
            return ErrUtil::errResp(ErrUtil::err_bad_parameters);
        }

        $detailModel = TripDetail::firstByAttributes(['tid' => $tid]);
        if ($detailModel) {
            $detailModel->gps_raw = $jsonStr;
            if ($detailModel->save()) {
                return ToolUtil::makeResp(null, 0);
            }
        }

        return ErrUtil::errResp(ErrUtil::err_general);
    }

    public function postRealtime()
    {
        $uid = Request::input('uid');
        $udid = Request::input('udid');
        $jam_id = Request::input('jam_id');
        $ign = Request::input('ignore');
        if (null == $udid) {
            return ErrUtil::errResp(ErrUtil::err_bad_parameters);
        }

        if (isset($ign) && $ign == 1 && isset($jam_id)) {
            $jamObj = RTTraffic::firstByAttributes(['jam_id' => $jam_id]);
            if ($jamObj) {
                $jamObj->ign = 1;
                if ($jamObj->save()) {
                    return ToolUtil::makeResp(["jam_id" => $jam_id], 0);
                }
            }
            return ErrUtil::errResp(ErrUtil::err_general);
        }

        $request = Request::instance();
        $jsonStr = ToolUtil::getContent($request);
        $jsonObj = json_decode($jsonStr);

        if (isset($jsonObj->st_date) && isset($jsonObj->st_lon) && isset($jsonObj->st_lat))
        {
            $stDate = ToolUtil::toDateTime($jsonObj->st_date);
            if (isset($jam_id)) {
                $jamObj = RTTraffic::firstByAttributes(['jam_id' => $jam_id]);
            }
            if (!isset($jamObj) || null == $jamObj) {
                $jamObj = RTTraffic::firstOrNew(['user_id' => $uid, 'device_id' => $udid, 'st_date' => $stDate]);
            }

            if (strlen($jamObj->jam_id) < 8) {
                $jamObj->jam_id = ToolUtil::geneUUID();
            }
            $jam_id = $jamObj->jam_id;

            $jamObj->st_date = $stDate;
            if (isset($jsonObj->ed_date)) {
                $jamObj->end_date = ToolUtil::toDateTime($jsonObj->ed_date);
                $jamObj->jam_duration = $jsonObj->ed_date - $jsonObj->st_date;
            }
            $jamObj->jam_start_lon = $jsonObj->st_lon * 100000;
            $jamObj->jam_start_lat = $jsonObj->st_lat * 100000;

            if (isset($jsonObj->user_lon)) $jamObj->user_lon = $jsonObj->user_lon * 100000;
            if (isset($jsonObj->user_lat)) $jamObj->user_lat = $jsonObj->user_lat * 100000;

            if (isset($jsonObj->ed_lon) && isset($jsonObj->ed_lat)) {
                $jamObj->jam_end_lon = $jsonObj->ed_lon * 100000;
                $jamObj->jam_end_lat = $jsonObj->ed_lat * 100000;

                $jamObj->jam_dist = BaiduHelper::GetDistance($jsonObj->st_lat, $jsonObj->st_lon, $jsonObj->ed_lat, $jsonObj->ed_lon);

                if (isset($jamObj->jam_duration) && $jamObj->jam_duration > 0) {
                    $jamObj->jam_speed = $jamObj->jam_dist/$jamObj->jam_duration;
                }
            }
            if (isset($jsonObj->waypoints)) $jamObj->way_points = $jsonObj->waypoints;

            if ($jamObj->save()) {
                return ToolUtil::makeResp(["jam_id" => $jam_id], 0);
            }
        }

        return ErrUtil::errResp(ErrUtil::err_general);
    }
}
