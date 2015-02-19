<?php

class TrafficController extends \BaseController {

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
		return $id;
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
	 * @param  int  $id
	 * @return Response
	 */
	public function getAbstract()
	{		
		$from = Request::input('from');
		$to = Request::input('to');

		$ak = 'ODpkCUvU6ICbiOikmOpm9H8Q'; 
		$sk = 'Rnjba5qwj6DkQIm9OnNGFAch0puiWbhn';
 
		//http://developer.baidu.com/map/index.php?title=car/api/driving
		$base_url = "http://api.map.baidu.com";
		$res_path = "/telematics/v3/navigation";
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
			'ak' => $ak,
			'region' => $region,
		);

		$sn = $this->caculateAKSN($sk, $res_path, $querystring_arrays);
		$querystring_arrays['sn'] = $sn;
		$querystring = http_build_query($querystring_arrays, null, "&");

		$target = $base_url . $res_path . '?' . $querystring;

		$client = new GuzzleHttp\Client();
		$res = $client->get($target);
		$statCode = $res->getStatusCode();
		if (200 == $statCode) {
			$json_resp = $res->json();
			$data = array('duration' => $json_resp['results'][0]['duration'], 'status' => 0);
			$result = array('code' => 0, 'data' => $data);
		} else {
			$result = array('code' => -1);
		}

		$response = Response::make(json_encode($result), 200);

		$response->header('Content-Type', 'application/json');

		return $response;
	}

	public function caculateAKSN($sk, $res_path, $querystring_arrays, $method = 'GET')
	{  
    	if ($method === 'POST'){  
        	ksort($querystring_arrays);  
    	}  
    	$querystring = http_build_query($querystring_arrays);  
    	return md5(urlencode($res_path.'?'.$querystring.$sk));  
	}

}
