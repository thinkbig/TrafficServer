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

    static public function getContent($request){
        $rawStr = $request->getContent();
        $encoding = $request->header('Content-Encoding');
        if ($encoding == "gzip") {
            return gzdecode($rawStr);
        }
        return $rawStr;
    }

    static public function toDateTime($unixTimestamp){
        return date("Y-m-d H:m:s", $unixTimestamp);
    }


    // helper functions for baidu api
    static public function getBaiduKey() {
        $ak = 'ODpkCUvU6ICbiOikmOpm9H8Q';
        $sk = 'Rnjba5qwj6DkQIm9OnNGFAch0puiWbhn';
        return array($ak, $sk);
    }

    static public function modifyRequestByBaiduKey($res_path, $querystring_arrays, $method = 'GET')
    {
        list($ak, $sk) = self::getBaiduKey();

        $querystring_arrays['ak'] = $ak;
        if ($method === 'POST'){
            ksort($querystring_arrays);
        }
        $querystring = http_build_query($querystring_arrays);
        $sn = md5(urlencode($res_path.'?'.$querystring.$sk));

        $querystring_arrays['sn'] = $sn;

        return $querystring_arrays;
    }


}