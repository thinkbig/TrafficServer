<?php
/**
 * Created by PhpStorm.
 * User: TaoQi
 * Date: 3/20/15
 * Time: 2:54 PM
 */

namespace App\Utils;


class BaiduHelper {

    static public function getRoadByInstruction($bdInstruction)
    {
        // 百度api会返回"从<b>起点</b>向正西方向出发,沿<b>丰茂巷</b>行驶190米,<b>左转</b>进入<b>琉璃街</b>"，这样的指示
        // 这里的正则是把当前路名筛选出来
        if (strlen($bdInstruction) > 5) {
            preg_match('/.*沿\<b\>(.+)\<\/b\>行驶.*/', $bdInstruction, $matches);
            if (count($matches) > 1) {
                $road = $matches[1];
                return $road;
            }
        }
        return null;
    }

    static public function rad($d)
    {
        return $d * 3.1415926535898 / 180.0;
    }

    static public function GetDistance($lat1, $lng1, $lat2, $lng2)
    {
        $EARTH_RADIUS = 6378137;    // 单位米
        $radLat1 = BaiduHelper::rad($lat1);
        $radLat2 = BaiduHelper::rad($lat2);
        $a = $radLat1 - $radLat2;
        $b = BaiduHelper::rad($lng1) - BaiduHelper::rad($lng2);
        $s = 2 * asin(sqrt(pow(sin($a/2.0),2) +
                cos($radLat1)*cos($radLat2)*pow(sin($b/2.0),2)));
        $s = $s *$EARTH_RADIUS;
        $s = round($s * 10000) / 10000.0;
        return $s;
    }

}