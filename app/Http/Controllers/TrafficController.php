<?php namespace App\Http\Controllers;

use Httpful;
use Request;
use Config;
use App\Utils\ToolUtil;
use App\Utils\ErrUtil;


// bd09ll表示百度经纬度坐标，bd09mc表示百度墨卡托坐标，gcj02表示经过国测局加密的坐标，wgs84表示gps获取的坐标。


class TrafficController extends Controller {

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

	/**
	 * Get the estimated abstract traffic info from loc1 to loc2.
	 *
	 * @param  String  from, to
	 * @return Response
	 */
	public function getAbstract()
	{
		$from = Request::input('from');
		$to = Request::input('to');

		//http://developer.baidu.com/map/index.php?title=car/api/driving
		$bd_config = Config::get('services.baidu');
        $base_url = $bd_config['base_url'];
		$res_path = $bd_config['res_navigation'];
		$output = "json";
		$from_coor = "bd09ll";
		$to_coor = "bd09ll";
		$region = "中国";
 		
		$querystring_arrays = array (
			'origin' => $from,
			'destination' => $to,
			'output' => $output,
			'coord_type' => $from_coor,
			'out_coord_type' => $to_coor,
			'region' => $region,
		);

        $querystring_arrays = ToolUtil::modifyRequestByBaiduKey($res_path, $querystring_arrays);
		$querystring = http_build_query($querystring_arrays, null, "&");

		$target = $base_url . $res_path . '?' . $querystring;

		$response = Httpful::get($target)->expectsJson()->send();
		if (200 == $response->code) {
			$data = array('duration' => $response->body->results[0]->duration, 'status' => 0);
            return ToolUtil::makeResp($data);
		}

		return ToolUtil::makeResp('服务异常，请稍后再试', -1);
	}

    public function postTrafficlight()
    {
        $request = Request::instance();
        $jsonStr = ToolUtil::getContent($request);
        $jsonObj = json_decode($jsonStr);

        $from = (isset($jsonObj->from) ? $jsonObj->from : null);
        $to = (isset($jsonObj->to) ? $jsonObj->to : null);
        if (null == $from || null == $to) {
            return ErrUtil::errResp(ErrUtil::err_bad_parameters);
        }
        $in_coor = (isset($jsonObj->in_coor) ? $jsonObj->in_coor : "coor_gps");
        $out_coor = (isset($jsonObj->out_coor) ? $jsonObj->out_coor : "coor_baidu");

        //http://developer.baidu.com/map/index.php?title=car/api/road
        $bd_config = Config::get('services.baidu');
        $base_url = $bd_config['base_url'];
        $res_path = $bd_config['res_viaPath'];
        $output = "json";
        $from_coor = $bd_config[$in_coor];
        $to_coor = $bd_config[$out_coor];

        if (null == $from_coor || null == $to_coor) {
            return ErrUtil::errResp(ErrUtil::err_bad_parameters);
        }

        $querystring_arrays = array (
            'origin' => $from,
            'destination' => $to,
            'output' => $output,
            'coord_type' => $from_coor,
            'out_coord_type' => $to_coor,
        );

        $querystring_arrays = ToolUtil::modifyRequestByBaiduKey($res_path, $querystring_arrays);
        $querystring = http_build_query($querystring_arrays, null, "&");

        $target = $base_url . $res_path . '?' . $querystring;
        $response = Httpful::get($target)->expectsJson()->send();
        if (200 == $response->code) {
            $result = $response->body->results;
            $trafficLight = isset($result->trafficLight) ? $result->trafficLight : null;
            $lights = array();
            foreach ($trafficLight as $light) {
                $location = isset($light->location) ? $light->location : null;
                if (isset($location->lng) && isset($location->lat)) {
                    $lights[] = ['lon' => $location->lng, 'lat' => $location->lat];
                }
            }

            return ToolUtil::makeResp(['coor' => 'coor_baidu', 'trafficlights' => $lights]);
        }

        return ErrUtil::errResp(ErrUtil::err_general);
    }
}
