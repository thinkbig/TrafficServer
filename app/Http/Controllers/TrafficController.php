<?php namespace App\Http\Controllers;

use App\Utils\GeoTransform;
use Httpful;
use Request;
use Config;
use App\Models\Trip;
use App\Utils\ToolUtil;
use App\Utils\FilterUtil;
use App\Utils\ErrUtil;
use App\Utils\BaiduHelper;

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

    public function getAbstractuser()
    {
        $udid = Request::input('udid');
        if (!ToolUtil::checkUdid($udid)) {
            ErrUtil::errResp(ErrUtil::err_authorize);
        }

        $fromId = Request::input('fromId');
        $toId = Request::input('toId');
        if (null == $fromId || null == $toId) {
            return $this->abstractBaidu($udid);
        }

        $start = Request::input('start');
        if (null == $start) {
            $startDate = new \DateTime();
        } else {
            $startDate = ToolUtil::toDateTime($start);
        }

        $bestRoute = $this->fetchBestJsonRoute($fromId, $toId, $startDate);
        if ($bestRoute) {
            $baiduRoute = json_decode($bestRoute);
            GeoTransform::convertRouteGps2Baidu($baiduRoute);
            $javaResult = $this->requestInternalAbs($udid, json_encode($baiduRoute), $startDate, 'user', 'baidu');
            if ($javaResult) {
                return ToolUtil::makeResp($javaResult);
            }
        } else {
            // 无法找到匹配的路径，尝试走百度api
            return $this->abstractBaidu($udid);
        }

        return ErrUtil::errResp(ErrUtil::err_java_internal);
    }

    public function getPredictuser()
    {
        $udid = Request::input('udid');
        if (!ToolUtil::checkUdid($udid)) {
            ErrUtil::errResp(ErrUtil::err_authorize);
        }

        $fromId = Request::input('fromId');
        $toId = Request::input('toId');

        $bestRoute = null;
        if ($fromId && $toId) {
            $start = Request::input('start');
            if (null == $start) {
                $startDate = new \DateTime();
            } else {
                $startDate = ToolUtil::toDateTime($start);
            }
            $bestRoute = $this->fetchBestJsonRoute($fromId, $toId, $startDate);
        }

        //http://121.43.230.8:8080/api/traffic/predictdeparturetime.s?PosFrom=120.00,30.1&PosTo=120.30,30.5
        $full_url = null;
        $coorType = 'baidu';
        $internal_url = "http://" . env('BACKEND_SERVER') . "/api/traffic/predictdeparturetime.s?coor=" . $coorType . "&udid=" . $udid;

        if ($bestRoute) {
            $baiduRoute = json_decode($bestRoute);
            GeoTransform::convertRouteGps2Baidu($baiduRoute);

            if (isset($baiduRoute->orig) && isset($baiduRoute->dest)) {
                $internal_url = $internal_url . '&PosFrom=' . $baiduRoute->orig->lon . ',' . $baiduRoute->orig->lat;
                $full_url = $internal_url . '&PosTo=' . $baiduRoute->dest->lon . ',' . $baiduRoute->dest->lat;
            }
        } else {
            $from = Request::input('from');
            $to = Request::input('to');

            list($code, $bd_data) = $this->requestBaidu($from, $to);
            if (ErrUtil::no_error == $code) {
                if (isset($bd_data['orig']) && isset($bd_data['dest'])) {
                    $orig = $bd_data['orig'];
                    $dest = $bd_data['dest'];
                    $internal_url = $internal_url . '&PosFrom=' . $orig['lon'] . ',' . $orig['lat'];
                    $full_url = $internal_url . '&PosTo=' . $dest['lon'] . ',' . $dest['lat'];
                }

            }
        }

        if ($full_url) {
            $internal_resp = Httpful::get($full_url)->expectsJson()->send();
            if (200 == $internal_resp->code && isset($internal_resp->body->code) && 0 == $internal_resp->body->code) {
                if (isset($internal_resp->body->data)) {
                    $data = $internal_resp->body->data;
                    return ToolUtil::makeResp($data);
                }
            }
        }

        return ErrUtil::errResp(ErrUtil::err_java_internal);
    }

    public function getFulluser()
    {
        $udid = Request::input('udid');
        if (!ToolUtil::checkUdid($udid)) {
            ErrUtil::errResp(ErrUtil::err_authorize);
        }

        $fromId = Request::input('fromId');
        $toId = Request::input('toId');
        if (null == $fromId || null == $toId) {
            return $this->fullBaidu($udid);
        }

        $start = Request::input('start');
        if (null == $start) {
            $startDate = new \DateTime();
        } else {
            $startDate = ToolUtil::toDateTime($start);
        }

        $bestRoute = $this->fetchBestJsonRoute($fromId, $toId, $startDate);
        if ($bestRoute) {
            $baiduRoute = json_decode($bestRoute);
            GeoTransform::convertRouteGps2Baidu($baiduRoute);
            $javaResult = $this->requestInternalFull($udid, json_encode($baiduRoute), $startDate, 'user', 'baidu');
            if ($javaResult) {
                return ToolUtil::makeResp($javaResult);
            }
        } else {
            // 无法找到匹配的路径，尝试走百度api
            return $this->fullBaidu($udid);
        }

        return ErrUtil::errResp(ErrUtil::err_java_internal);
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
        $in_coor = (isset($jsonObj->in_coor) ? $jsonObj->in_coor : "gps");
        $out_coor = (isset($jsonObj->out_coor) ? $jsonObj->out_coor : "baidu");

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
            if (is_array($trafficLight)) {
                foreach ($trafficLight as $light) {
                    $location = isset($light->location) ? $light->location : null;
                    if (isset($location->lng) && isset($location->lat)) {
                        $lights[] = ['lon' => $location->lng, 'lat' => $location->lat];
                    }
                }
            }

            return ToolUtil::makeResp(['coor' => 'baidu', 'trafficlights' => $lights]);
        }

        return ErrUtil::errResp(ErrUtil::err_general);
    }

    public function getJamszone()
    {
        $udid = Request::input('udid');
        if (!ToolUtil::checkUdid($udid)) {
            ErrUtil::errResp(ErrUtil::err_authorize);
        }

        $lonFromTo = Request::input('lonFromTo');
        $latFromTo = Request::input('latFromTo');
        if (null == $lonFromTo || null == $latFromTo) {
            return ErrUtil::errResp(ErrUtil::err_bad_parameters);
        }
        $coor = Request::input('coor');
        if (null == $coor) {
            $coor = 'baidu';
        }

        $stTimtstamp = Request::input('start');
        if (null == $stTimtstamp) {
            $stTimtstamp = time();
        }
        $internal_url = "http://" . env('BACKEND_SERVER') . "/api/traffic/jamsinzone.s?coor=" . $coor;
        $internal_url = sprintf("%s&udid=%s&startDate=%u&lonFromTo=%s&latFromTo=%s", $internal_url, $udid, $stTimtstamp, $lonFromTo, $latFromTo);
        $internal_resp = Httpful::get($internal_url)->expectsJson()->send();

        if (200 == $internal_resp->code && isset($internal_resp->body->code) && 0 == $internal_resp->body->code) {
            if (isset($internal_resp->body->data)) {
                $data = $internal_resp->body->data;
                return ToolUtil::makeResp($data);
            }
        }

        return ErrUtil::errResp(ErrUtil::err_java_internal);
    }



    public function abstractBaidu($udid)
    {
        $from = Request::input('from');
        $to = Request::input('to');

        list($code, $bd_data) = $this->requestBaidu($from, $to);

        if (ErrUtil::no_error != $code) {
            return ErrUtil::errResp($code);
        }

        $jsonStr = json_encode($bd_data);
        if ($jsonStr) {
            $javaResult = $this->requestInternalAbs($udid, $jsonStr, new \DateTime(), 'baidu', 'baidu');
            if ($javaResult) {
                return ToolUtil::makeResp($javaResult);
            }
            return ErrUtil::errResp(ErrUtil::err_java_internal);
        }

        return ErrUtil::errResp(ErrUtil::err_baidu);
    }

    public function fullBaidu($udid)
    {
        $from = Request::input('from');
        $to = Request::input('to');

        list($code, $bd_data) = $this->requestBaidu($from, $to);

        if (ErrUtil::no_error != $code) {
            return ErrUtil::errResp($code);
        }

        $jsonStr = json_encode($bd_data);
        if ($jsonStr) {
            $javaResult = $this->requestInternalFull($udid, $jsonStr, new \DateTime(), 'baidu', 'baidu');
            if ($javaResult) {
                return ToolUtil::makeResp($javaResult);
            }
            return ErrUtil::errResp(ErrUtil::err_java_internal);
        }

        return ToolUtil::makeResp('服务异常，请稍后再试', -1);
    }

    private function requestBaidu($from, $to)
    {
        $from_pt = explode(",", $from);
        $to_pt = explode(",", $to);
        if (count($from_pt) != 2 || count($to_pt) != 2) {
            return array(ErrUtil::err_bad_parameters, null);
        }

        //http://developer.baidu.com/map/index.php?title=car/api/driving
        // 百度api的例子
        // http://api.map.baidu.com/telematics/v3/navigation?origin=%E6%96%B9%E6%B4%B2%E5%B0%8F%E5%AD%A6&destination=%E8%8B%8F%E5%B7%9E%E4%B9%90%E5%9B%AD&region=%E8%8B%8F%E5%B7%9E&output=json&ak=7ZNN5imWdinViWWmBGA3Rlx5
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
        if (200 == $response->code && 20 == $response->body->returnType)
        {
            $bd_during = 0;
            $bd_dist = 0;
            $steps = array();

            if (isset($response->body->results)) {
                $bd_results = $response->body->results;
                $firstOrig = null;
                $lastDest = null;
                foreach ($bd_results as $bd_route)
                {
                    $dist = $bd_route->distance;
                    $during = $bd_route->duration;
                    $bd_steps = $bd_route->steps;
                    foreach ($bd_steps as $bd_step)
                    {
                        $step = array();
                        $step['distance'] = $bd_step->distance;
                        $step['duration'] = $bd_step->duration;
                        $step['intro'] = BaiduHelper::getRoadByInstruction($bd_step->instructions);
                        $step['path'] = $bd_step->path;
                        $step['from'] = ['lon' => $bd_step->stepOriginLocation->lng, 'lat' => $bd_step->stepOriginLocation->lat];
                        $step['to'] = ['lon' => $bd_step->stepDestinationLocation->lng, 'lat' => $bd_step->stepDestinationLocation->lat];

                        $steps[] = $step;
                    }

                    $bd_during += $during;
                    $bd_dist += $dist;

                    if (null == $firstOrig) {
                        $firstOrig = ['lon' => $bd_route->originLocation->lng, 'lat' => $bd_route->originLocation->lat];
                    }
                    $lastDest = ['lon' => $bd_route->destinationlocation->lng, 'lat' => $bd_route->destinationlocation->lat];
                }
                $data = ['distance' => $bd_dist, 'duration' => $bd_during, 'steps' => $steps,
                    'orig' => $firstOrig, 'dest' => $lastDest];

                return array(ErrUtil::no_error, $data);
            } else {
                return array(ErrUtil::err_baidu, $response->body->returnType);
            }

        } else {
            return array(ErrUtil::err_baidu, $response->code);
        }

        return array(ErrUtil::err_bad_parameters, null);
    }

    private function requestInternalAbs($udid, $routeStr, $stDate, $source, $coorType)
    {
        $stTimtstamp = $stDate->getTimestamp();
        $internal_url = "http://" . env('BACKEND_SERVER') . "/api/traffic/abstract.s?coor=" . $coorType;
        $internal_url = sprintf("%s&udid=%s&startDate=%u&source=%s", $internal_url, $udid, $stTimtstamp, $source);
        $json_body = $routeStr;
        $internal_resp = Httpful::post($internal_url)->body($json_body)->expectsJson()->send();

        if (200 == $internal_resp->code && isset($internal_resp->body->code) && 0 == $internal_resp->body->code) {
            if (isset($internal_resp->body->data)) {
                $data = $internal_resp->body->data;
                $data->coor_type = $coorType;
                return $data;
            }
        }

        return null;
    }

    private function requestInternalFull($udid, $routeStr, $stDate, $source, $coorType)
    {
        $stTimtstamp = $stDate->getTimestamp();
        $internal_url = "http://" . env('BACKEND_SERVER') . "/api/traffic/full.s?coor=" . $coorType;
        $internal_url = sprintf("%s&udid=%s&startDate=%u&source=%s", $internal_url, $udid, $stTimtstamp, $source);
        $json_body = $routeStr;
        $internal_resp = Httpful::post($internal_url)->body($json_body)->expectsJson()->send();

        if (200 == $internal_resp->code && isset($internal_resp->body->code) && 0 == $internal_resp->body->code) {
            if (isset($internal_resp->body->data)) {
                $data = $internal_resp->body->data;
                $data->coor_type = $coorType;
                return $data;
            }
        }

        return null;
    }

    private function fetchBestJsonRoute($fromId, $toId, $startDate)
    {
        $trips = Trip::where(['st_parkingId' => $fromId, 'ed_parkingId' => $toId])
            ->whereNotNull('key_route')
            ->orderBy('st_date','DESC')
            ->take(8)
            ->get();

        $bestRoute = null;
        if (count($trips) > 0)
        {
            $filterArr = array();
            $isWeekend = FilterUtil::isWeekend($startDate);
            $passWeekFilter = false;
            $passTimeFilter = false;
            $passQualityFilter = false;
            $avgDuration = 0;
            foreach ($trips as $trip)
            {
                if (strlen($trip->key_route) < 10 || $trip->total_during <= 0) {
                    continue;
                }
                $avgSpeed = $trip->total_dist/$trip->total_during;
                if ($avgSpeed < 5.0/3.6 || $avgSpeed > 300.0/3.6) {
                    continue;
                }

                $date = new \DateTime($trip->st_date) ;

                // check weekend
                $weekend = FilterUtil::isWeekend($date);
                if (!$passWeekFilter) {
                    $passWeekFilter = ($weekend == $isWeekend);
                }

                // check time
                $diff = $date->diff($startDate);
                $diff_minites = $diff->h * 60 + $diff->i;
                if (!$passTimeFilter) {
                    $passTimeFilter = abs($diff_minites) < 120;
                }

                // check quality
                if (!$passQualityFilter) {
                    $passQualityFilter = $trip->quality >= 1 && $trip->quality <= 2;
                }

                // calculate avgDuration
                $avgDuration += $trip->total_during;

                $filterArr[] = ['time_diff' => $diff_minites, 'weekend' => $weekend,
                    'distance' => $trip->total_dist, 'duration' => $trip->total_during,
                    'quality' => $trip->quality, 'route' => $trip->key_route];
            }
            $avgDuration = $avgDuration/count($trips);

//            usort($filterArr, function($a, $b){
//                $qa = $a['quality'];
//                $qb = $b['quality'];
//                return ($qa < $qb) ? -1 : 1;
//            });

            if (count($filterArr) > 0) {
                $bestRoute = $filterArr[0]['route'];
            }
            foreach ($filterArr as $tripDict) {
                if ($passWeekFilter && ($tripDict['weekend'] != $isWeekend)) {
                    continue;
                }
                if ($passTimeFilter && (abs($tripDict['time_diff']) >= 120)) {
                    continue;
                }
                if ($passQualityFilter && ($tripDict['quality'] < 1 || $tripDict['quality'] > 2)) {
                    continue;
                }
                $curDuration = $tripDict['duration'];
                $bestRoute = $tripDict['route'];
                if (abs($curDuration-$avgDuration)/$avgDuration < 0.3) {
                    break;
                }
            }
        }

        return $bestRoute;
    }

}
