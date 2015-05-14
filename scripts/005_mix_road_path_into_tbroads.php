<?php
/**
 * Created by PhpStorm.
 * User: TaoQi
 * Date: 4/19/15
 * Time: 8:45 PM
 */

// 把所有的road_id_map表中的roadpath（latitudes，longitudes）合并到tbroads，变成 lat,lon|lat,lon|lat,lon

$dbArr = array("110000", "120000", "130100", "140100", "210100", "220100", "310000", "320100", "320200", "320600",
    "330100", "330200", "330300", "330400", "330700", "331000", "350100", "350200", "350500", "370100",
    "370200", "420100", "430100", "440100", "440300", "440400", "441900", "442000", "500000", "510100",
    "530100", "610100");

// 1：GPS设备获取的角度坐标; 2：GPS获取的米制坐标、sogou地图所用坐标; 3：google地图、soso地图、aliyun地图、mapabc地图和amap地图所用坐标
// 4：3中列表地图坐标对应的米制坐标 5：百度地图采用的经纬度坐标 6：百度地图采用的米制坐标
// 7：mapbar地图坐标; 8：51地图坐标

$conDest = mysqli_connect("121.40.193.34", "root", "cardraw.chetu@314159", 'cm_user');
mysqli_query($conDest,"SET NAMES utf8");

for ($i = 0; $i < sizeof($dbArr); $i++)
{
    $dbName = $dbArr[$i];
    $con = mysqli_connect("121.40.193.34", "root", "cardraw.chetu@314159", $dbName);
    mysqli_query($con,"SET NAMES utf8");

    $limit = 50;
    $offset = 0;
    while (1) {
        $sql_select = "select * from road_id_map where longitudes IS NOT NULL AND latitudes IS NOT NULL limit $offset,$limit";
        $offset += $limit;
        $db_result = mysqli_query($con, $sql_select);

        $reqArr = array();
        $isFinished = true;
        while($obj = $db_result->fetch_object()){
            $isFinished = false;
            $road_id = substr($obj->road_id, 1, strlen($obj->road_id)-1);
            $latitudes = explode(',', $obj->latitudes);
            $longitudes = explode(',', $obj->longitudes);
            $cnt = count($latitudes);
            $mixLoc = '';
            $sep = '';
            for ($j=0; $j<$cnt; $j++) {
                $mixLoc .= $sep . $latitudes[$j] . ',' . $longitudes[$j];
                $sep = '|';
            }

            $insData = array(
                'way_points' => $mixLoc,
                'updated_at' => date("Y-m-d H:i:s")
            );

            $select_result = mysqli_query($conDest, "select count(*) from back_tbroads where road_id='$road_id'");
            $row = $select_result->fetch_row();
            if (0 != $row[0]) {
                $query = 'UPDATE back_tbroads SET ';
                $sep = '';
                foreach($insData as $key=>$value) {
                    $query .= $sep.$key.' = "'.$value.'"';
                    $sep = ',';
                }
                $query .= " WHERE road_id='$road_id'";
                mysqli_query($conDest, $query);
            }
        }

        if ($isFinished) {
            echo $dbName . " db end mix \n";
            break;
        }
    };

    mysqli_close($con);
}

mysqli_close($conDest);



