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
        $uidStr = "test_user_0000001";
        $cid = "test_car_0000001";

        // add seeding user
        $userObj = User::firstOrNew(['user_string' => $uidStr]);
        $userObj->latest_device = $udid;
        $userObj->latest_car = $cid;
        $userObj->name = 'Thinkbig';
        $userObj->email = '87149798@qq.com';
        $userObj->phone = '17095011032';
        $userObj->password = 'fakeanddefaultpassword';
        $userObj->intro = '测试账号';
        $userObj->experience = 0;

        $userObj->save();

        $uid = $userObj->id;

        // add seeding device
        $deviceObj = Device::firstOrNew(['udid' => $udid]);
        $deviceObj->user_id = $uid;
        $deviceObj->device_type = Device::IOS;
        $deviceObj->device_token = '';
        $deviceObj->version = 1;
        $deviceObj->source = Device::Internal;
        $deviceObj->device_info = "Tao's iPhone Fake";
        $deviceObj->country_code = 'CN';

        $deviceObj->save();

        // add seeding car
        $carObj = Car::firstOrNew(['cid' => $cid]);
        $carObj->user_id = $uid;
        $carObj->car_no = '苏E23Y51';
        $carObj->license_no = '';
        $carObj->engine_no = '';
        $carObj->car_company = '大众';
        $carObj->car_brand = '斯柯达';
        $carObj->date_buy = new \DateTime('2009-07-01');
        $carObj->car_info = '橙色，1.4排量，自动挡';

        $carObj->save();

    }

}