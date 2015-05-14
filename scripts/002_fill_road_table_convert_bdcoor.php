<?php
/**
 * Created by PhpStorm.
 * User: TaoQi
 * Date: 3/17/15
 * Time: 2:16 PM
 */

// 为每个road_id_map表 百度坐标列，填充数据，通过百度api
// http://developer.baidu.com/map/changeposition.htm
// http://api.map.baidu.com/geoconv/v1/?coords=114.21892734521,29.575429778924;114.21892734521,29.575429778924&ak=7ZNN5imWdinViWWmBGA3Rlx5&from=1&to=5

$dbArr = array("110000", "120000", "130100", "140100", "210100", "220100", "310000", "320100", "320200", "320600",
    "330100", "330200", "330300", "330400", "330700", "331000", "350100", "350200", "350500", "370100",
    "370200", "420100", "430100", "440100", "440300", "440400", "441900", "442000", "500000", "510100",
    "530100", "610100");

// 1：GPS设备获取的角度坐标; 2：GPS获取的米制坐标、sogou地图所用坐标; 3：google地图、soso地图、aliyun地图、mapabc地图和amap地图所用坐标
// 4：3中列表地图坐标对应的米制坐标 5：百度地图采用的经纬度坐标 6：百度地图采用的米制坐标
// 7：mapbar地图坐标; 8：51地图坐标

foreach ($dbArr as $dbName)
{
    $con = mysqli_connect("121.40.193.34", "root", "cardraw.chetu@314159", $dbName);

    $offset = 0;
    while (1) {
        $sql_select = "select * from road_id_map where (bd_start_x IS NULL or bd_end_x IS NULL) and (start_x IS NOT NULL and end_x IS NOT NULL) limit 40";
        //$sql_select = "select * from road_id_map limit $offset,40";
        $offset += 40;
        $db_result = mysqli_query($con, $sql_select);

        $url = "http://api.map.baidu.com/geoconv/v1/?ak=7ZNN5imWdinViWWmBGA3Rlx5&from=3&to=5&coords=";

        $reqArr = array();
        while($obj = $db_result->fetch_object()){
            $start_x = $obj->start_x;
            $start_y = $obj->start_y;
            $end_x = $obj->end_x;
            $end_y = $obj->end_y;
            $id = $obj->no;

            if ($start_x && $start_y) {
                $reqArr[] = array('id' => $id, 'lon' => $start_x, 'lat' => $start_y, 'start' => true);
            }
            if ($end_x && $end_y) {
                $reqArr[] = array('id' => $id, 'lon' => $end_x, 'lat' => $end_y, 'start' => false);
            }
        }

        if (count($reqArr) == 0) {
            echo $dbName . " db end convert \n";
            break;
        }

        // start reqeust
        $param = array();
        foreach ($reqArr as $req) {
            $param[] = sprintf("%.6f,%.6f", $req['lon'], $req['lat']);
        }

        $urlNew = $url . implode(";",$param);

        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, $urlNew);
        curl_setopt($cURL, CURLOPT_HTTPGET, true);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($cURL);
        $json = json_decode($result, true);

        if (0 == $json['status']) {
            $respArr = $json['result'];
            for ($i = 0; $i < sizeof($respArr); $i++) {
                $orig = $reqArr[$i];
                $orig_id = $orig['id'];
                $bd_x = $respArr[$i]['x'];
                $bd_y = $respArr[$i]['y'];

                if ($orig['start']) {
                    $sql_update = "update road_id_map set bd_start_x = $bd_x, bd_start_y = $bd_y where no = $orig_id";
                } else {
                    $sql_update = "update road_id_map set bd_end_x = $bd_x, bd_end_y = $bd_y where no = $orig_id";
                }

                echo $sql_update . "\n";
                mysqli_query($con, $sql_update);
            }
        }

        curl_close($cURL);
        echo mysqli_error($con);
    };

    mysqli_close($con);
    //break;
}


