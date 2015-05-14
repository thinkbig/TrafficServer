<?php

// 为每个road_id_map表增加百度坐标列

$dbArr = array("110000", "120000", "130100", "140100", "210100", "220100", "310000", "320100", "320200", "320600",
               "330100", "330200", "330300", "330400", "330700", "331000", "350100", "350200", "350500", "370100",
               "370200", "420100", "430100", "440100", "440300", "440400", "441900", "442000", "500000", "510100",
               "530100", "610100");


foreach ($dbArr as $dbName)
{
    $con = mysqli_connect("121.40.193.34", "root", "cardraw.chetu@314159", $dbName);

    // alter table
    $sql_alter = "ALTER TABLE road_id_map
                  ADD COLUMN bd_start_x DOUBLE AFTER latitudes,
                  ADD COLUMN bd_start_y DOUBLE AFTER bd_start_x,
                  ADD COLUMN bd_end_x DOUBLE AFTER bd_start_y,
                  ADD COLUMN bd_end_y DOUBLE AFTER bd_end_x;
                  ";

//    $sql_alter = "ALTER TABLE road_id_map
//                  CHANGE COLUMN bd_start_x bd_start_x DOUBLE NULL DEFAULT NULL,
//                  CHANGE COLUMN bd_start_y bd_start_y DOUBLE NULL DEFAULT NULL,
//                  CHANGE COLUMN bd_end_x bd_end_x DOUBLE NULL DEFAULT NULL,
//                  CHANGE COLUMN bd_end_y bd_end_y DOUBLE NULL DEFAULT NULL;
//                  ";

    mysqli_query($con, $sql_alter);

    echo mysqli_error($con);

    mysqli_close($con);
}



// crete table
//$sql = "CREATE TABLE road_id_map
//(
//no int,
//road_name varchar(128),
//road_id varchar(128),
//orig_road_id varchar(128),
//start_name varchar(128),
//end_name varchar(128),
//dir varchar(64),
//road_level int,
//start_x float,
//start_y float,
//end_x float,
//end_y float,
//longitudes text,
//latitudes text
//)";
//mysqli_query($con, $sql);



