<?php namespace App\Http\Controllers;

use Request;
use App\Utils\ToolUtil;
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
            return ToolUtil::makeResp(null, -1);
        }

        $request = Request::instance();
        $jsonStr = $request->getContent();
        $jsonObj = json_decode($jsonStr);
        $dna = md5($jsonStr);

        if (isset($jsonObj->gps_lat) && isset($jsonObj->gps_lon))
        {
            $newAttr = array(
                'user_id' => $uid,
                'device_id'    => $udid,
                'dna'    => $dna,
                'nearby_poi' => (isset($jsonObj->nearby_poi) ? $jsonObj->nearby_poi : null),
                'user_mark' => (isset($jsonObj->user_mark) ? $jsonObj->user_mark : null),
                'province' => (isset($jsonObj->province) ? $jsonObj->province : null),
                'city' => (isset($jsonObj->city) ? $jsonObj->city : null),
                'district' => (isset($jsonObj->district) ? $jsonObj->district : null),
                'street' => (isset($jsonObj->street) ? $jsonObj->street : null),
                'street_num' => (isset($jsonObj->street_num) ? $jsonObj->street_num : null),
                'gps_lon' => $jsonObj->gps_lon,
                'gps_lat' => $jsonObj->gps_lat
            );
            if (strlen($pid) > 8) {
                $newAttr['pid'] = $pid;
                UserParking::updateOrCreate(array('pid' => $pid, 'user_id' => $uid, 'device_id' => $udid), $newAttr);
            } else {
                $modelBuilder = UserParking::where(array('user_id' => $uid, 'device_id' => $udid, 'dna' => $dna));
                $model = $modelBuilder->first();
                if ($model && strlen($model->pid) > 8) {
                    $newAttr['pid'] = $model->pid;
                    $pid = $model->pid;
                    $modelBuilder->update($newAttr);
                } else {
                    $pid = ToolUtil::geneUUID();
                    $newAttr['pid'] = $pid;
                    UserParking::create($newAttr);
                }
            }

            $result = ["pid" => $pid];
            return ToolUtil::makeResp($result, 0);
        }

        return ToolUtil::makeResp(null, -1);
    }

}
