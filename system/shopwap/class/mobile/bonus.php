<?php
$member = get_member_account(true,true);
$status = $_GP['status'];
$bonuslist = array();
$today = time();
$hasbonus = 0;
if(!empty($member['openid'])){
    $openid = $member['openid'];
    $member = member_get($openid);
    $bonus = mysqld_selectall("select bonus_user.*,bonus_type.type_name,bonus_type.type_money,bonus_type.use_start_date,bonus_type.use_end_date from " . table("bonus_user") . " bonus_user left join  " . table("bonus_type") . " bonus_type on bonus_type.type_id=bonus_user.bonus_type_id where bonus_user.deleted=0  and `openid`=:openid order by isuse,bonus_type.send_type ", array(':openid' => $openid));
    if(!empty($bonus))
        $hasbonus = 1;
    foreach ($bonus as $bonu){
        if(0 == intval($status)){
            if(0 == intval($bonu['isuse']) && $bonu['use_end_date'] >= $today){
                $bonuslist[] = $bonu;
            }
        }else{
            if($bonu['use_end_date'] < $today || 1 == intval($bonu['isuse'])){
                $bonuslist[] = $bonu;
            }
        }
    }
}

include themePage('bonuslist');