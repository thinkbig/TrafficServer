<?php namespace App\Utils;
/**
 * Created by PhpStorm.
 * User: TaoQi
 * Date: 2/27/15
 * Time: 9:54 PM
 */

use Response;

class ToolUtil
{

    static public function geneUUID()
    {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        }

        mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);
        return $uuid;
    }

    static public function makeResp($content, $code = 0)
    {
        $dataTag = 'msg';
        if (0 == $code) {
            $dataTag = 'data';
        }
        $resultArr = array('code' => $code, $dataTag => $content);
        $jsonStr = json_encode($resultArr);
        $resp = Response::make($jsonStr, 200);
        $resp->header('Content-Type', 'application/json');
        return $resp;
    }

    static public function toDateTime($unixTimestamp){
        return date("Y-m-d H:m:s", $unixTimestamp);
    }

}