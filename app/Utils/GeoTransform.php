<?php namespace App\Utils;
/**
 * Created by PhpStorm.
 * User: TaoQi
 * Date: 4/17/15
 * Time: 1:20 PM
 */

class LocationCoor {
    public $lat;
    public $lon;
}


class GeoTransform {

    const X_PI = 52.3598775598;      //3.14159265358979324 * 3000.0 / 180.0;
    const M_PI = 3.14159265358979323846264338327950288;
    const M_A = 6378245.0;// WGS 长轴半径
    const M_EE = 0.00669342162296594323;// WGS 偏心率的平方

    public static function outOfChina($location) {
        if ($location->lon < 72.004 || $location->lon > 137.8347) {
            return true;
        }
        if ($location->lat < 0.8293 || $location->lat > 55.8271) {
            return true;
        }
        return false;
    }

    public static function earth2Mars($location) {
        if (GeoTransform::outOfChina($location)) {
            return $location;
        }
        $dLat = GeoTransform::transformLat($location->lon - 105.0, $location->lat - 35.0);
        $dLon = GeoTransform::transformLon($location->lon - 105.0, $location->lat - 35.0);
        $radLat = $location->lat / 180.0 * self::M_PI;
        $magic = sin($radLat);
        $magic = 1 - self::M_EE * $magic * $magic;
        $sqrtMagic = sqrt($magic);
        $dLat = ($dLat * 180.0) / ((self::M_A * (1 - self::M_EE)) / ($magic * $sqrtMagic) * self::M_PI);
        $dLon = ($dLon * 180.0) / (self::M_A / $sqrtMagic * cos($radLat) * self::M_PI);

        $location->lat = $location->lat + $dLat;
        $location->lon = $location->lon + $dLon;
        return $location;
    }

    public static function mars2Baidu($locatioin) {
        $x = $locatioin->lon;
        $y = $locatioin->lat;
        $z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * self::X_PI);
        $theta = atan2($y, $x) + 0.000003 * cos($x * self::X_PI);

        $bdLocation = new LocationCoor();
        $bdLocation->lon = $z * cos($theta) + 0.0065;
        $bdLocation->lat = $z * sin($theta) + 0.006;
        return $bdLocation;
    }

    public static function baidu2Mars($locatioin) {
        $x = $locatioin->lon - 0.0065;
        $y = $locatioin->lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * self::X_PI);
        $theta = atan2($y, $x) - 0.000003 * cos($x * self::X_PI);

        $marsLocation = new LocationCoor();
        $marsLocation->lon = $z * cos($theta);
        $marsLocation->lat = $z * sin($theta);
        return $marsLocation;
    }

    public static function earth2Baidu($locatioin) {
        $marsLoc = self::earth2Mars($locatioin);
        return self::mars2Baidu($marsLoc);
    }

    private static function transformLat($lon, $lat) {
        $ret = -100.0 + 2.0 * $lon + 3.0 * $lat + 0.2 * $lat * $lat + 0.1 * $lon * $lat + 0.2 * sqrt(abs($lon));
        $ret += (20.0 * sin(6.0 * $lon * self::M_PI) + 20.0 * sin(2.0 * $lon * self::M_PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($lat * self::M_PI) + 40.0 * sin($lat / 3.0 * self::M_PI)) * 2.0 / 3.0;
        $ret += (160.0 * sin($lat / 12.0 * self::M_PI) + 320 * sin($lat * self::M_PI / 30.0)) * 2.0 / 3.0;
        return $ret;
    }

    private static function transformLon($lon, $lat) {
        $ret = 300.0 + $lon + 2.0 * $lat + 0.1 * $lon * $lon + 0.1 * $lon * $lat + 0.1 * sqrt(abs($lon));
        $ret += (20.0 * sin(6.0 * $lon * self::M_PI) + 20.0 * sin(2.0 * $lon * self::M_PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($lon * self::M_PI) + 40.0 * sin($lon / 3.0 * self::M_PI)) * 2.0 / 3.0;
        $ret += (150.0 * sin($lon / 12.0 * self::M_PI) + 300.0 * sin($lon / 30.0 * self::M_PI)) * 2.0 / 3.0;
        return $ret;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////

    public static function convertRouteGps2Baidu(&$routeObj)
    {
        if ($routeObj->coor_type == 'baidu') {
            return;
        }

        $routeObj->coor_type = 'baidu';
        if (isset($routeObj->orig)) {
            $newLoc = self::earth2Baidu($routeObj->orig);
            $routeObj->orig->lat = $newLoc->lat;
            $routeObj->orig->lon = $newLoc->lon;
        }
        if (isset($routeObj->dest)) {
            $newLoc = self::earth2Baidu($routeObj->dest);
            $routeObj->dest->lat = $newLoc->lat;
            $routeObj->dest->lon = $newLoc->lon;
        }
        if (isset($routeObj->steps)) {
            foreach ($routeObj->steps as $step) {
                $fromLoc = self::earth2Baidu($step->from);
                $step->from->lat = $fromLoc->lat;
                $step->from->lon = $fromLoc->lon;

                $toLoc = self::earth2Baidu($step->to);
                $step->to->lat = $toLoc->lat;
                $step->to->lon = $toLoc->lon;

                // path: "120.61483,31.21801"
                if (isset($step->path)) {
                    $pathArr = explode(';', $step->path);
                    $newPathArr = array();
                    foreach ($pathArr as $pathPt) {
                        $pt = explode(',', $pathPt);
                        $loc = new LocationCoor();
                        $loc->lon = $pt[0];
                        $loc->lat = $pt[1];

                        $toLoc = self::earth2Baidu($loc);
                        $newPathArr[] = $toLoc->lon . ',' . $toLoc->lat;
                    }
                    $step->path = implode(";", $newPathArr);
                }

                // convert jams
                if (isset($step->jams) && is_array($step->jams)) {
                    foreach ($step->jams as $jam) {
                        $fromJam = self::earth2Baidu($jam->from);
                        $jam->from->lat = $fromJam->lat;
                        $jam->from->lon = $fromJam->lon;

                        $toJam = self::earth2Baidu($jam->to);
                        $jam->to->lat = $toJam->lat;
                        $jam->to->lon = $toJam->lon;
                    }
                }
            }
        }
    }

}