<?php
$member = get_member_account(true,true);
$status = $_GP['status'];
$bonuslist = array();
$today = time();
$hasbonus = 0;
if(!empty($member['openid'])){
    $openid = $member['openid'];
    $member = member_get($openid);
    $bonus = mysqld_selectall("select bonus_user.*,bonus_type.type_name,bonus_type.type_money,bonus_type.use_start_date,bonus_type.use_end_date from " . table("bonus_user") . " bonus_user left join  " . table("bonus_type") . " bonus_type on bonus_type.type_id=bonus_user.bonus_type_id where bonus_user.deleted=0  and bonus_user.openid=:openid order by bonus_user.isuse,bonus_type.send_type ", array(':openid' => $openid));
    //2017-03-21-新增红包里的优惠券
    $package_bonus= mysqld_selectall("select pbu.*, pb.bonus_name as type_name, pb.bonus_money as type_money from ".table("package_bonus_user")." pbu left join ".table("package_bonus")." pb on pb.bonus_id = pbu.package_bonus_id where pbu.deleted=0 and pbu.openid=:openid order by pbu.isuse,pb.bonus_send_type ", array(':openid' => $openid));
    //end
    if((is_array($bonus) && !empty($bonus)) || (is_array($package_bonus) && !empty($package_bonus))) {//2017-03-21-新增红包优惠券
        $hasbonus = 1;
        foreach ($bonus as $bonu) {
            if (0 == intval($status)) {
                if (0 == intval($bonu['isuse']) && $bonu['use_end_date'] >= $today) {
                    $bonuslist[] = $bonu;
                }
            } else {
                if ($bonu['use_end_date'] < $today || 1 == intval($bonu['isuse'])) {
                    $bonuslist[] = $bonu;
                }
            }
        }
        foreach ($package_bonus as $package_bonu) {
            if (0 == intval($status)) {
                if (0 == intval($package_bonu['isuse']) && $package_bonu['use_end_date'] >= $today) {
                    $bonuslist[] = $package_bonu;
                }
            } else {
                if ($package_bonu['use_end_date'] < $today || 1 == intval($package_bonu['isuse'])) {
                    $bonuslist[] = $package_bonu;
                }
            }
        }
    }
}

include themePage('bonuslist');