<?php
/**
 * Created by PhpStorm.
 * User: TaoQi
 * Date: 2/28/15
 * Time: 8:27 PM
 */

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\User;
use App\Models\Car;

class UserSeeder extends Seeder {

    public function run()
    {
        $udid = "test_device_0000001";
        $uid = "test_user_0000001";
        $cid = "test_car_0000001";

        Device::updateOrCreate(array('udid' => $udid), array(
            'udid'     => $udid,
            'user_id' => $uid,
            'device_type'    => Device::IOS,
            'device_token' => "",
            'version' => 1,     // 1.0.0
            'source'    => Device::Internal,
            'device_info' => "Tao's iPhone Fake",
            'country_code' => 'CN'
        ));

        Car::updateOrCreate(array('cid' => $cid), array(
            'cid'     => $cid,
            'user_id' => $uid,
            'car_no'  => '苏E23Y51',
            'license_no' => "",
            'engine_no' => "",
            'car_company' => "大众",
            'car_brand' => "斯柯达",
            'date_buy' => new \DateTime('2009-07-01'),
            'car_info' => "橙色，1.4排量，自动挡",
        ));

        User::updateOrCreate(array('uid' => $uid), array(
            'uid'     => $uid,
            'latest_device' => $udid,
            'latest_car' => $cid,
            'name'  => 'Thinkbig',
            'email' => "87149798@qq.com",
            'phone' => "17095011032",
            'password' => "fakeanddefaultpassword",
            'intro' => "测试账号",
            'experience' => 0,
        ));
    }

}