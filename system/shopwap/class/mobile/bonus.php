<?php
$member=get_member_account(true,true);

if(!empty($member['weixin_openid'])){
    $weixin_openid = $member['weixin_openid'];
    $member = member_get_by_weixin($weixin_openid);
    $bonuslist = mysqld_selectall("select bonus_user.*,bonus_type.type_name,bonus_type.type_money,bonus_type.use_start_date,bonus_type.use_end_date from " . table("bonus_user") . " bonus_user left join  " . table("bonus_type") . " bonus_type on bonus_type.type_id=bonus_user.bonus_type_id where bonus_user.deleted=0  and `weixin_openid`=:weixin_openid order by isuse,bonus_type.send_type ", array(':weinxin_openid' => $weixin_openid));
}else if(!empty($member['openid'])){
    $openid = $member['openid'];
    $member = member_get($openid);
    $bonuslist = mysqld_selectall("select bonus_user.*,bonus_type.type_name,bonus_type.type_money,bonus_type.use_start_date,bonus_type.use_end_date from " . table("bonus_user") . " bonus_user left join  " . table("bonus_type") . " bonus_type on bonus_type.type_id=bonus_user.bonus_type_id where bonus_user.deleted=0  and `openid`=:openid order by isuse,bonus_type.send_type ", array(':openid' => $openid));
}

include themePage('bonuslist');