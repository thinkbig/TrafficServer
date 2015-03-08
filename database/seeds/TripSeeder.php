<?php
/**
 * Created by PhpStorm.
 * User: TaoQi
 * Date: 3/4/15
 * Time: 11:06 AM
 */

use Illuminate\Database\Seeder;
use App\Models\UserParking;
use App\Models\Trip;
use App\Models\TripDetail;

class TripSeeder extends Seeder {

    public function run()
    {
        $udid = "test_device_0000001";
        $uid = "test_user_0000001";
        $pid1 = "test_parkingLoc_0000001";
        $pid2 = "test_parkingLoc_0000002";
        $tid = "test_trip_0000001";

        UserParking::updateOrCreate(array('pid' => $pid1), array(
            'pid'     => $pid1,
            'user_id' => $uid,
            'device_id'    => $udid,
            'nearby_poi' => '起点某小区',
            'dna' => 'fake_dna_1',
            'user_mark' => '家',
            'rate'    => 0,
            'province' => '江苏省',
            'city' => '苏州市',
            'district' => '园区',
            'street' => '现代大道',
            'street_num' => 'xx号',
            'gps_lon' => 120.626470,
            'gps_lat' => 31.222380,
            'baidu_lon' => 120.626470,
            'baidu_lat' => 31.222380,
            'nav_id' => null,
            'circle_id' => null
        ));
        UserParking::updateOrCreate(array('pid' => $pid2), array(
            'pid'     => $pid2,
            'user_id' => $uid,
            'device_id'    => $udid,
            'dna' => 'fake_dna_2',
            'nearby_poi' => '终点某小区',
            'user_mark' => '公司',
            'rate'    => 0,
            'province' => '江苏省',
            'city' => '苏州市',
            'district' => '新区',
            'street' => '狮山路',
            'street_num' => 'xx号',
            'gps_lon' => 120.752460,
            'gps_lat' => 31.331180,
            'baidu_lon' => 120.752460,
            'baidu_lat' => 31.331180,
            'nav_id' => null,
            'circle_id' => null
        ));

        Trip::updateOrCreate(array('tid' => $tid), array(
            'tid'     => $tid,
            'user_id' => $uid,
            'device_id'    => $udid,
            'dna' => 'fake_dna_3',
            'st_date' => new \DateTime('2010-07-05T06:00:00Z'),
            'end_date' => new \DateTime('2010-07-05T06:40:00Z'),
            'st_parkingId'    => $pid1,
            'ed_parkingId'    => $pid2,
            'total_dist' => 30198.3,
            'total_during' => 18000.3,
            'max_speed' => 32.1,
            'avg_speed' => 14.1,
            'traffic_jam_dist' => 900,
            'traffic_jam_during' => 421.1,
            'traffic_avg_speed' => 2.3,
            'traffic_light_tol_cnt' => 12,
            'traffic_light_jam_cnt' => 5,
            'traffic_light_waiting' => 312,
            'traffic_heavy_jam_cnt' => 1,
            'traffic_jam_max_during' => 180
        ));

        TripDetail::updateOrCreate(array('tid' => $tid), array(
            'tid'     => $tid,
            'detail' => '{"data":"detail data"}',
            'gps_raw'    => '["one gps record", "another gps record"]'
        ));

    }

}