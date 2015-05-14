<?php
/**
 * Created by PhpStorm.
 * User: TaoQi
 * Date: 3/17/15
 * Time: 2:16 PM
 */

use App\Models\TBRoad;

// 给所有的路的log表加上时间索引

$dbArr = array("110000", "120000", "130100", "140100", "210100", "220100", "310000", "320100", "320200", "320600",
    "330100", "330200", "330300", "330400", "330700", "331000", "350100", "350200", "350500", "370100",
    "370200", "420100", "430100", "440100", "440300", "440400", "441900", "442000", "500000", "510100",
    "530100", "610100");

// 1：GPS设备获取的角度坐标; 2：GPS获取的米制坐标、sogou地图所用坐标; 3：google地图、soso地图、aliyun地图、mapabc地图和amap地图所用坐标
// 4：3中列表地图坐标对应的米制坐标 5：百度地图采用的经纬度坐标 6：百度地图采用的米制坐标
// 7：mapbar地图坐标; 8：51地图坐标

for ($i = 0; $i < sizeof($dbArr); $i++)
{
    $dbName = $dbArr[$i];
    $con = mysqli_connect("121.40.193.34", "root", "cardraw.chetu@314159", $dbName);
    mysqli_query($con,"SET NAMES utf8");

    $table_result = mysqli_query($con, "show tables like 'X%'");
    echo mysqli_error($con);
    while($obj = $table_result->fetch_object()) {
        $array = get_object_vars($obj);
        $val_arr = array_values($array);
        $tbl_name = $val_arr[0];

        $alter_sql = "ALTER TABLE $tbl_name ADD INDEX ind1 (date ASC);";
        mysqli_query($con, $alter_sql);

        echo $tbl_name . " add index done \n";
    }

    echo $dbName . " add index done ######################## \n";
    mysqli_close($con);
}



