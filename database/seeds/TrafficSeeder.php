<?php
/**
 * Created by PhpStorm.
 * User: TaoQi
 * Date: 3/18/15
 * Time: 1:04 PM
 */

use Illuminate\Database\Seeder;
use App\Models\RTTraffic;

class TrafficSeeder extends Seeder {

    public function run()
    {
        $uid = 1;
        $udid = "test_device_0000001";
        $jam_id = "test_jamLoc_0000001";
        $stDate = new \DateTime('2010-07-05T06:00:00Z');

        $jamObj = RTTraffic::firstOrNew(['user_id' => $uid, 'device_id' => $udid, 'st_date' => $stDate]);
        if ($jamObj->jam_id != $jam_id) {
            $jamObj->jam_id = $jam_id;
            $jamObj->st_date = $stDate;
            $jamObj->end_date = new \DateTime('2010-07-05T06:10:00Z');;
            $jamObj->jam_start_lon = 12075246;
            $jamObj->jam_start_lat = 3133118;
            $jamObj->jam_end_lon = 12075246;
            $jamObj->jam_end_lat = 3133118;
            $jamObj->user_lon = 12075246;
            $jamObj->user_lat = 3133118;
            $jamObj->jam_start_bdlon = 12075246;
            $jamObj->jam_start_bdlat = 3133118;
            $jamObj->jam_end_bdlon = 12075246;
            $jamObj->jam_end_bdlat = 3133118;
            $jamObj->user_bdlon = 12075246;
            $jamObj->user_bdlat = 3133118;
            $jamObj->jam_duration = 180;
            $jamObj->jam_speed = 11.1;
            $jamObj->jam_dist = 300;
            $jamObj->way_points = "120.75246,31.33118|120.75246,31.33118|120.75246,31.33118";

            $jamObj->save();
        }
    }
}