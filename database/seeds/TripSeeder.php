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
        $uid = 1;
        $udid = "test_device_0000001";
        $pid1 = "test_parkingLoc_0000001";
        $pid2 = "test_parkingLoc_0000002";
        $tid = "test_trip_0000001";

        $parkStObj = UserParking::firstOrNew(['user_id' => $uid, 'device_id' => $udid, 'gps_lon' => 12062647, 'gps_lat' => 3122238]);
        if ($parkStObj->pid != $pid1) {
            $parkStObj->pid = $pid1;
            $parkStObj->nearby_poi = '起点某小区';
            $parkStObj->user_mark = '家';
            $parkStObj->rate = 0;
            $parkStObj->province = '江苏省';
            $parkStObj->city = '苏州市';
            $parkStObj->district = '园区';
            $parkStObj->street = '现代大道';
            $parkStObj->street_num = 'xx号';
            $parkStObj->gps_lon = 12062647;
            $parkStObj->gps_lat = 3122238;
            $parkStObj->baidu_lon = 12062647;
            $parkStObj->baidu_lat = 3122238;
            $parkStObj->nav_id = null;
            $parkStObj->circle_id = null;

            $parkStObj->save();
        }

        $parkEdObj = UserParking::firstOrNew(['user_id' => $uid, 'device_id' => $udid, 'gps_lon' => 12075246, 'gps_lat' => 3133118]);
        if ($parkEdObj->pid != $pid2) {
            $parkEdObj->pid = $pid2;
            $parkEdObj->nearby_poi = '终点某小区';
            $parkEdObj->user_mark = '公司';
            $parkEdObj->rate = 0;
            $parkEdObj->province = '江苏省';
            $parkEdObj->city = '苏州市';
            $parkEdObj->district = '新区';
            $parkEdObj->street = '狮山路';
            $parkEdObj->street_num = 'xx号';
            $parkEdObj->gps_lon = 12075246;
            $parkEdObj->gps_lat = 3133118;
            $parkEdObj->baidu_lon = 12075246;
            $parkEdObj->baidu_lat = 3133118;
            $parkEdObj->nav_id = null;
            $parkEdObj->circle_id = null;

            $parkEdObj->save();
        }

        $stDate = new \DateTime('2010-07-05T06:00:00Z');
        $tripObj = Trip::firstOrNew(['user_id' => $uid, 'device_id' => $udid, 'st_date' => $stDate]);
        if ($tripObj->tid != $tid) {
            $tripObj->tid = $tid;
            $tripObj->st_date = $stDate;
            $tripObj->end_date = new \DateTime('2010-07-05T07:40:00Z');
            $tripObj->st_parkingId = $pid1;
            $tripObj->ed_parkingId = $pid2;
            $tripObj->total_dist = 30198.3;
            $tripObj->total_during = 18000.3;
            $tripObj->max_speed = 32.1;
            $tripObj->avg_speed = 14.1;
            $tripObj->traffic_jam_dist = 900;
            $tripObj->traffic_jam_during = 421.1;
            $tripObj->traffic_avg_speed = 2.3;
            $tripObj->traffic_light_tol_cnt = 12;
            $tripObj->traffic_light_jam_cnt = 5;
            $tripObj->traffic_light_waiting = 312;
            $tripObj->traffic_heavy_jam_cnt = 1;
            $tripObj->traffic_jam_max_during = 180;

            $tripObj->save();
        }

        $tripDetailObj = TripDetail::firstOrNew(['tid' => $tid]);
        $tripDetailObj->tid = $tid;
        $tripDetailObj->detail = '{"data":"detail data"}';
        $tripDetailObj->gps_raw = '["one gps record", "another gps record"]';

        $tripDetailObj->save();

    }

}