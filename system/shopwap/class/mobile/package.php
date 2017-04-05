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
            $packages = mysqld_select("select * from " . table('package') . " where order_id=:order_id ", array(':order_id' => intval($_GP['orderid'])));
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
                if($packages['users_number'] != 0) {
                    //$haspackagebonus = mysqld_selectall("select * from " . table('package_bonus_user') . " where openid=:openid and weixin_openid=:weixin_openid and deleted = 0 ", array(':openid' => $member['openid'], ':weixin_openid' => $member['weixin_openid']));
                    //2017-04-03-yanru-可能因为读取访问页面微信用户的信息会重新分配一个用户openid，所以暂时只用weixin_openid作为查询条件
                    $haspackagebonus = mysqld_selectall("select * from " . table('package_bonus_user') . " where weixin_openid=:weixin_openid and deleted = 0 ", array(':weixin_openid' => $member['weixin_openid']));
                    //2017-04-05-yanru-添加对分享链接的时间判断，不能超过两天
                    $orderCreateTime = mysqld_selectcolumn("SELECT createtime FROM " . table('shop_order') . " WHERE id =  ".intval($_GP['orderid']));
                    $orderLinkLastTime = strtotime(date('Y-m-d', $orderCreateTime).'+ 2 day');
                    //end
                    if ($orderLinkLastTime > time()) {
                        if (empty($haspackagebonus)) {
                            if (is_array($package_bonus) && !empty($package_bonus)) {
                                foreach ($package_bonus as $bonus) {
                                    $bonus_sn = date("Ymd", time()) . $rank_model['type_id'] . rand(1000000, 9999999);
                                    $data = array('package_bonus_id' => $bonus['bonus_id'],
                                        'bonus_sn' => $bonus_sn,
                                        'openid' => $member['openid'],
                                        'package_id' => $bonus['package_id'],
                                        'weixin_openid' => $member['weixin_openid'],
                                        'eable_days' => $bonus['eable_days'],
                                        'use_start_date' => time(),
                                        'use_end_date' => strtotime('+' . $bonus['eable_days'] . ' day'));
                                    mysqld_insert('package_bonus_user', $data);
                                }
                            }
                            $users_number = $packages['users_number'] - 1;
                            mysqld_update('package', array('users_number' => $users_number), array('order_id' => intval($_GP['orderid'])));
                        }
                    }else{
                        message('抱歉，链接已过期!');
                    }
                }else{
                    message('抱歉，你来晚了!');
                }
            }
            $shopwap_weixin_share = $shopwap_weixin_share = weixin_share('package',array('orderid'=>intval($_GP['orderid']))
                ,"我领取和团团全场通用优惠券-各地方特产",WEB_ROOT.'/themes/default/_RESOURCE_/image/weixin_bonus_share.jpg',"等你来尝鲜",$settings);
            if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
                include WEB_ROOT.'/system/common/template/mobile/weixinshare.php';
            }
            include themePage('sharebonus');
            exit;
        }else{
            message('抱歉，购买商品后才可以获得优惠大礼包!');
        }
    }
}else {
    message('抱歉，请使用微信访问!');
}