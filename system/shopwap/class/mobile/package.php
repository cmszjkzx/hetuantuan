<?php
/**
 * Created by PhpStorm.
 * User: yanru02
 * Date: 2017/3/28
 * Time: 9:52
 */
if (is_use_weixin()) {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
        $weixin_openid = get_weixin_openid();
        member_login_weixin($weixin_openid);
        $weixin_wxfans = mysqld_select("SELECT * FROM " . table('weixin_wxfans') . " WHERE weixin_openid=:weixin_openid ", array(':weixin_openid' =>$weixin_openid));
        $member = mysqld_select("SELECT * FROM " . table('member') . " where weixin_openid=:weixin_openid or openid=:openid limit 1", array(':openid' => $weixin_wxfans['openid'], ':weixin_openid' => $weixin_openid));

        $order_id = intval($_GP['orderid']);
        $customer_name = $member['nickname'];

        $package_bonus = mysqld_selectall("select * from ".table('package_bonus')." where deleted = 0 ");
        $package_id = $package_bonus[0]['package_id'];
        $max_amount = $package_bonus[0]['max_amount'];
        $max_number = $package_bonus[0]['max_number'];
        for ($i = 0; $i < count($package_bonus); $i++){
            $package_bonus[$i]['use_end_date'] = date("Y-m-d",strtotime('+'.$package_bonus[$i]['eable_days'].' day'));
        }

        if(!empty($_GP['orderid'])) {
            $packages = mysqld_select("select * from " . table('package') . " where openid=:openid and weixin_openid=:weixin_openid and order_id=:order_id ", array(':openid' => $member['openid'], ':weixin_openid' => $member['weixin_openid'], ':order_id' => intval($_GP['orderid'])));
            if(empty($packages)){
                $data = array(
                    'package_id' => $package_id,
                    'openid' => $member['openid'],
                    'weixin_openid' => $member['weixin_openid'],
                    'max_number' => $max_amount,
                    'users_number' => $max_number,
                    'order_id' => intval($_GP['orderid']),
                    'create_time' => time()
                );
                mysqld_insert('package', $data);
            }else{
                $haspackagebonus = mysqld_selectall("select * from ".table('package_bonus_user')." where openid=:openid and weixin_openid=:weixin_openid and deleted = 0 ", array(':openid'=>$member['openid'], ':weixin_openid'=>$member['weixin_openid']));
                if(empty($haspackagebonus)){
                    if(is_array($package_bonus) && !empty($package_bonus)){
                        foreach ($package_bonus as $bonus){
                            $bonus_sn=date("Ymd",time()).$rank_model['type_id'].rand(1000000,9999999);
                            $data = array('package_bonus_id' => $bonus['bonus_id'],
                                'bonus_sn' => $bonus_sn,
                                'openid' => $member['openid'],
                                'package_id' => $bonus['package_id'],
                                'weixin_openid' => $member['weixin_openid'],
                                'eable_days'=>$bonus['eable_days'],
                                'use_start_date' => time(),
                                'use_end_date' => strtotime('+'.$bonus['eable_days'].' day'));
                            mysqld_insert('package_bonus_user', $data);
                        }
                    }
                }
            }
            include themePage('sharebonus');
        }else{
            message('抱歉，购买商品后才可以获得优惠大礼包!');
        }
    }
}else {
    message('抱歉，请使用微信访问!');
}