<?php
/**
 * Created by PhpStorm.
 * User: yanru02
 * Date: 2016/11/23
 * Time: 14:46
 */

$member=get_member_account();
$openid =$member['openid'] ;
$weixin_openid = $member['weixin_openid'];
if(!empty($_GP['id'])){
    $adv = mysqld_select("SELECT * FROM " . table('shop_adv') . " WHERE  id = ".$_GP['id']);
    if(checksubmit('submit')){
        $phone = $_GP['phone'];
        if(!empty($phone)){
            $adv_bonus = array();
            if(!empty($adv['hasbonus'])){
                $adv_bonus = explode('-',$adv['hasbonus']);
                array_shift($adv_bonus);
            }
            foreach ($adv_bonus as $bonusid) {
                if (!empty($openid)) {
                    $user_bonus = mysqld_select("SELECT * FROM " . table('bonus_user') . " WHERE  openid = ".$openid." AND bonus_type_id = ".$bonusid);
                    if(empty($user_bonus)){
                        $bonus_sn = date("Ymd", time()) . $rank_model['type_id'] . rand(1000000, 9999999);
                        $data = array('createtime' => time(),
                            'openid' => $openid,
                            'weixin_openid' => $weixin_openid,
                            'bonus_sn' => $bonus_sn,
                            'deleted' => 0,
                            'isuse' => 0,
                            'bonus_type_id' => $bonusid);
                        mysqld_insert('bonus_user', $data);
                    }
                }
                header("location: " . mobile_url('bonus'));
            }
        }else{
            message("请输入手机号!");
        }
    }
}else{
    $adv = mysqld_select("SELECT * FROM " . table('shop_adv') . " WHERE  displayorder = 99");
    if(checksubmit('submit')){
        $phone = $_GP['phone'];
        if(!empty($phone)){
            $adv_bonus = array();
            if(!empty($adv['hasbonus'])){
                $adv_bonus = explode('-',$adv['hasbonus']);
                array_shift($adv_bonus);
            }
            foreach ($adv_bonus as $bonusid) {
                if (!empty($openid)) {
                    $user_bonus = mysqld_select("SELECT * FROM " . table('bonus_user') . " WHERE  openid = ".$openid." AND bonus_type_id = ".$bonusid);
                    if(empty($user_bonus)){
                        $bonus_sn = date("Ymd", time()) . $rank_model['type_id'] . rand(1000000, 9999999);
                        $data = array('createtime' => time(),
                            'openid' => $openid,
                            'weixin_openid' => $weixin_openid,
                            'bonus_sn' => $bonus_sn,
                            'deleted' => 0,
                            'isuse' => 0,
                            'bonus_type_id' => $bonusid);
                        mysqld_insert('bonus_user', $data);
                    }
                }
                header("location: " . mobile_url('bonus'));
            }
        }else{
            message("请输入手机号!");
        }
    }
}

include themePage('getbonus');