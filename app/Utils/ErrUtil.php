<?php
/**
 * Created by PhpStorm.
 * User: TaoQi
 * Date: 3/16/15
 * Time: 10:30 AM
 */

namespace App\Utils;

class ErrUtil {

    // device type define
    const no_error = 0;
    const err_general = 1;
    const err_bad_parameters = 2;
    const err_authorize = 3;
    const err_baidu = 4;
    const err_java_internal = 5;

    static public function errResp($errType)
    {
        $resp = null;
        switch ($errType) {
            case self::err_bad_parameters:
                $resp = ToolUtil::makeResp("请求参数错误", $errType);
                break;
            case self::err_baidu:
                $resp = ToolUtil::makeResp("第三方服务错误", $errType);
                break;
            case self::err_authorize:
                $resp = ToolUtil::makeResp("校验错误", $errType);
                break;
            case self::err_java_internal:
                $resp = ToolUtil::makeResp("内部服务错误", $errType);
                break;
            default:
                $resp = ToolUtil::makeResp("服务异常，请稍后再试", self::err_general);
        }
        return $resp;
    }

}