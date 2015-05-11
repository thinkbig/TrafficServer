<?php namespace App\Utils;
/**
 * Created by PhpStorm.
 * User: TaoQi
 * Date: 2/27/15
 * Time: 9:54 PM
 */

use Response;
use Httpful;
use App\Models\Device;

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

    static public function httpfulJsonArr()
    {
        $json_handler = new Httpful\Handlers\JsonHandler(array('decode_as_array' => true));
        Httpful\Httpful::register('application/json', $json_handler);
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
        return date("Y-m-d H:i:s", $unixTimestamp);
    }


    // helper functions for baidu api
    static public function getBaiduKey()
    {
        $bdKeys = array(['距离规划', 'ODpkCUvU6ICbiOikmOpm9H8Q', 'Rnjba5qwj6DkQIm9OnNGFAch0puiWbhn'],
                        ['车图备用0001', 'vuFb1CMFQgUYArIBCd9NltS7', '9aAAffx99r77ah13SwEy6MCgpQNGydUr'],
                        ['车图备用0002', 'Wf6vZD0Nez9G5eSfwttvhcK5', 'vvVfF60lfRvpYnjVmSh2qzj8R6zGgUiN'],
                        ['车图备用0003', 'G8SOISX5OyyQA9jjQtlnD4La', '5EqQ6ZWaDR8ON7KIDZfkLH8Dz8Fo5XL7'],
                        ['车图备用0004', 'hAg1keTI0Q9WoU1DWMxvEi9R', 'wLnfXOxPRpaO2hf8WoyzhMyNYPq0ddLB'],
                        ['车图备用0005', 'wHGuAbVHmfrFH3fmUphDGcO8', 'iHb9AVBst5lLRgET4R93t3WOnciMNVSM'],
                        ['车图备用0006', 'nz6Ghu0lZ7mVG2RmZzZChDly', 'DsMFZz8IuP5bgStVpOQVivg0s4l3RRVF'],
                        ['车图备用0007', 'T7TBS82WaqgcTDtXCPdxyr45', 'EV4LRNNMePD00l4wa3kgBQPEyu5HpqN9'],
                        ['车图备用0008', 'EmupzPQkgz4jELvVrNciTCFa', 'QBXgdMCmS1LCXabT5Eb2aPOWmDw8oXzA'],
                        ['车图备用0009', 'xIqqyIGmO6GhjiGPzQqlxrwr', 'Sm3ouX0G2ENWHAb6a8GSGaj7By8XeO61'],
                        ['车图备用0010', 'OBEp4Q9GcgRHPvVIq66KFjC3', 'ynY1jPQAvjN9hTtfLL2eh5ImBNOxbP4Q']
        );

        $randIdx = rand(0, count($bdKeys)-1);
        $realKey = $bdKeys[$randIdx];
        return [$realKey[1], $realKey[2]];
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

    static public function checkUdid($udid)
    {
        if (!isset($udid) || null == $udid) {
            return false;
        }
        $device = Device::where(['udid' => $udid])->first();
        if ($device) {
            // check redis for frequent
            return true;
        }
        return false;
    }

}