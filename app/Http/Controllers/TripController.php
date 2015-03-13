<?php namespace App\Http\Controllers;

use Request;
use App\Utils\ToolUtil;
use App\Models\Trip;
use App\Models\TripDetail;

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
            return ToolUtil::makeResp(null, -1);
        }

        $request = Request::instance();
        $jsonStr = ToolUtil::getContent($request);
        $jsonObj = json_decode($jsonStr);
        $dna = md5($jsonStr);

        if (isset($jsonObj->start_date) && isset($jsonObj->end_date) && isset($jsonObj->st_parkingId) && isset($jsonObj->ed_parkingId))
        {
            $newAttr = array(
                'user_id' => $uid,
                'device_id'    => $udid,
                'dna' => $dna,
                'st_date' => ToolUtil::toDateTime($jsonObj->start_date),
                'end_date' => ToolUtil::toDateTime($jsonObj->end_date),
                'st_parkingId'    => $jsonObj->st_parkingId,
                'ed_parkingId'    => $jsonObj->ed_parkingId,
                'total_dist' => (isset($jsonObj->total_dist) ? $jsonObj->total_dist : 0),
                'total_during' => (isset($jsonObj->total_during) ? $jsonObj->total_during : 0),
                'max_speed' => (isset($jsonObj->max_speed) ? $jsonObj->max_speed : 0),
                'avg_speed' => (isset($jsonObj->avg_speed) ? $jsonObj->avg_speed : 0),
                'traffic_jam_dist' => (isset($jsonObj->traffic_jam_dist) ? $jsonObj->traffic_jam_dist : 0),
                'traffic_jam_during' => (isset($jsonObj->traffic_jam_during) ? $jsonObj->traffic_jam_during : 0),
                'traffic_avg_speed' => (isset($jsonObj->traffic_avg_speed) ? $jsonObj->traffic_avg_speed : 0),
                'traffic_light_tol_cnt' => (isset($jsonObj->traffic_light_tol_cnt) ? $jsonObj->traffic_light_tol_cnt : 0),
                'traffic_light_jam_cnt' => (isset($jsonObj->traffic_light_jam_cnt) ? $jsonObj->traffic_light_jam_cnt : 0),
                'traffic_light_waiting' => (isset($jsonObj->traffic_light_waiting) ? $jsonObj->traffic_light_waiting : 0),
                'traffic_heavy_jam_cnt' => (isset($jsonObj->traffic_heavy_jam_cnt) ? $jsonObj->traffic_heavy_jam_cnt : 0),
                'traffic_jam_max_during' => (isset($jsonObj->traffic_jam_max_during) ? $jsonObj->traffic_jam_max_during : 0)
            );

            if (strlen($tid) > 8) {
                $newAttr['tid'] = $tid;
                Trip::updateOrCreate(array('tid' => $tid, 'user_id' => $uid, 'device_id' => $udid), $newAttr);
            } else {
                $modelBuilder = Trip::where(array('user_id' => $uid, 'device_id' => $udid, 'dna' => $dna));
                $model = $modelBuilder->first();
                if ($model && strlen($model->tid) > 8) {
                    $newAttr['tid'] = $model->tid;
                    $tid = $model->tid;
                    $modelBuilder->update($newAttr);
                } else {
                    $tid = ToolUtil::geneUUID();
                    $newAttr['tid'] = $tid;
                    Trip::create($newAttr);
                }
            }

            TripDetail::updateOrCreate(array('tid' => $tid), array(
                'tid'     => $tid,
                'detail' => $jsonStr,
            ));
            $detailModel = TripDetail::firstByAttributes(['tid' => $tid]);
            $hasRaw = (isset($detailModel->gps_raw) && strlen($detailModel->gps_raw)>0);

            $result = ["tid" => $tid, "has_raw" => $hasRaw];
            return ToolUtil::makeResp($result, 0);
        }

        return ToolUtil::makeResp(null, -1);
    }


    public function postRaw()
    {
        // 这里将来要加验证，否则上报就没有限制，很容易被脏数据攻击
        $tid = Request::input('tid');
        $request = Request::instance();
        $jsonStr = ToolUtil::getContent($request);
        if (null == $tid || strlen($jsonStr) < 10) {
            return ToolUtil::makeResp(null, -1);
        }

        $detailModel = Trip::firstByAttributes(['tid' => $tid]);
        if ($detailModel) {
            TripDetail::updateOrCreate(['tid' => $tid], ['tid' => $tid, 'gps_raw' => $jsonStr]);
            return ToolUtil::makeResp(null, 0);
        }

        return ToolUtil::makeResp(null, -1);
    }
}
