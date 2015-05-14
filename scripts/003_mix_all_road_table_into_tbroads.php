<?php
/**
 * Created by PhpStorm.
 * User: TaoQi
 * Date: 3/17/15
 * Time: 2:16 PM
 */

// 把所有的road_id_map整合成一张表bakc_tbroads，从而方便快速查询

$dbArr = array("110000", "120000", "130100", "140100", "210100", "220100", "310000", "320100", "320200", "320600",
    "330100", "330200", "330300", "330400", "330700", "331000", "350100", "350200", "350500", "370100",
    "370200", "420100", "430100", "440100", "440300", "440400", "441900", "442000", "500000", "510100",
    "530100", "610100");

$nameArr = array("北京市", "天津市", "石家庄市", "太原市", "沈阳市", "长春市", "上海市", "南京市", "无锡市", "南通市",
    "杭州市", "宁波市", "温州市", "嘉兴市", "金华市", "台州市", "福州市", "厦门市", "泉州市", "济南市",
    "青岛市", "武汉市", "长沙市", "广州市", "深圳市", "珠海市", "东莞市", "中山市", "重庆市", "成都市",
    "昆明市", "西安市");

// 1：GPS设备获取的角度坐标; 2：GPS获取的米制坐标、sogou地图所用坐标; 3：google地图、soso地图、aliyun地图、mapabc地图和amap地图所用坐标
// 4：3中列表地图坐标对应的米制坐标 5：百度地图采用的经纬度坐标 6：百度地图采用的米制坐标
// 7：mapbar地图坐标; 8：51地图坐标

//$conDest = mysqli_connect("127.0.0.1", "root", "chetu@123", 'cm_user');
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
        $sql_select = "select * from road_id_map where bd_start_x IS NOT NULL AND bd_end_x IS NOT NULL limit $offset,$limit";
        $offset += $limit;
        $db_result = mysqli_query($con, $sql_select);

        $reqArr = array();
        $isFinished = true;
        while($obj = $db_result->fetch_object()){
            $isFinished = false;
            $road_id = substr($obj->road_id, 1, strlen($obj->road_id)-1);
            $insData = array(
                'road_id'     => $road_id,
                'road_name' => $obj->road_name,
                'city_id'    => $dbName,
                'city_name' => $nameArr[$i],
                'start_name' => $obj->start_name,
                'end_name' => $obj->end_name,
                'dir'    => $obj->dir,
                'road_level' => $obj->road_level,
                'coor_type' => 'baidu',
                'start_lon' => $obj->bd_start_x * 100000,
                'start_lat' => $obj->bd_start_y * 100000,
                'end_lon' => $obj->bd_end_x * 100000,
                'end_lat' => $obj->bd_end_y * 100000,
                'updated_at' => date("Y-m-d H:i:s")
            );

            $select_result = mysqli_query($conDest, "select count(*) from back_tbroads where road_id='$road_id'");
            $row = $select_result->fetch_row();
            if (0 == $row[0]) {
                $insData['created_at'] = date("Y-m-d H:i:s");
                $fields = array();
                $values = array();
                foreach( array_keys($insData) as $key ) {
                    $fields[] = "`$key`";
                    $values[] = "'" . mysqli_real_escape_string($conDest, $insData[$key]) . "'";
                }
                $columnStr = implode(",", $fields);
                $valueStr  = implode(",", $values);
                $sql = "INSERT INTO back_tbroads($columnStr) VALUES ($valueStr)";
                mysqli_query($conDest, $sql);
            } else {
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



