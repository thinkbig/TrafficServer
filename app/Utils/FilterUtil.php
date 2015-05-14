<?php namespace App\Utils;
/**
 * Created by PhpStorm.
 * User: TaoQi
 * Date: 4/15/15
 * Time: 1:47 PM
 */

class FilterUtil {

    static public function isWeekend($date)
    {
        return $date->format('N') >= 6;
    }

    static public function isNationalHoliday($date)
    {
        return false;
    }

}