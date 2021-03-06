<?php
$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
if($operation=='post') {
    $bonus = mysqld_select("SELECT * FROM " . table('package_bonus')." where bonus_id='".intval($_GP['bonus_id'])."' " );
    if($bonus['bonus_send_type']==0) {
        $rank_model_list = mysqld_selectall("SELECT * FROM " . table('rank_model')." order by rank_level" );

        if (checksubmit('send_user')) {

            foreach ($_GP['user_add'] as &$id) {

                if($_GP['search_type'] == 'phone'){
                    $member = mysqld_select("SELECT * FROM " . table('member') . "where istemplate=0 and mobile=:mobile limit 1", array(":mobile" => $id));
                }else {
                    $member = mysqld_select("SELECT * FROM " . table('member') . "where istemplate=0 and weixin_openid=:weixin_openid limit 1", array(":weixin_openid" => $id));
                }

                if(!empty($member['openid'])) {
                    $bonus_sn=date("Ymd",time()).$rank_model['type_id'].rand(1000000,9999999);//生成随机的优惠券号
//          $bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')."where bonus_sn='".$bonus_sn."'" );
//				  while(!empty($bonus_user['bonus_id']))
//					{
//            $bonus_sn=date("Ymd",time()).$rank_model['type_id'].rand(1000000,9999999);
//					  $bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')."where bonus_sn='".$bonus_sn."'" );
//					}
            //2016-11-21-yanru-begin
                    $bonus_user = mysqld_select("SELECT * FROM " . table('package_bonus_user')."where package_bonus_id='".intval($_GP['bonus_id'])."' and openid='".$member['openid']."' " );
                    if(!empty($bonus_user)){
                        message("用户已经领取该优惠券！");
                    }else {
                        //end
                        $data = array('package_bonus_id' => $_GP['bonus_id'],
                            'bonus_sn' => $bonus_sn,
                            'openid' => $member['openid'],
                            'package_id' => $bonus['package_id'],
                            'weixin_openid' => $member['weixin_openid'],
                            'eable_days'=>$bonus['eable_days'],
                            'use_start_date' => time(),
                            'use_end_date' => strtotime('+2 day'));
                        mysqld_insert('package_bonus_user', $data);
                        message("发放成功！","refresh","success");
                    }
                }
            }
        }
        if (checksubmit('send_userleve')) {
            if(empty($_GP['bonus_id'])) {
                message("无法获取到优惠券信息");
            }
            if(empty($_GP['rank_level'])) {
                message('请选择用户等级。');
            }
            if(intval($_GP['rank_level'])==-1) {
                $member_list = mysqld_selectall("SELECT * FROM " . table('member')." where istemplate=0 ");
                foreach ($member_list as &$member) {
                    $bonus_sn=date("Ymd",time()).rand(0,9).rand(1000000,9999999);
                    $bonus_user = mysqld_select("SELECT * FROM " . table('package_bonus_user')."where bonus_sn='".$bonus_sn."'" );
                    while(!empty($bonus_user['package_bonus_id'])) {
                        $bonus_sn=date("Ymd",time()).rand(0,9).rand(1000000,9999999);
                        $bonus_user = mysqld_select("SELECT * FROM " . table('package_bonus_user')."where bonus_sn='".$bonus_sn."'" );
                    }
                    $data = array('package_bonus_id' => $_GP['bonus_id'],
                        'bonus_sn' => $bonus_sn,
                        'openid' => $member['openid'],
                        'package_id' => $bonus['package_id'],
                        'weixin_openid' => $member['weixin_openid'],
                        'eable_days'=>$bonus['eable_days'],
                        'use_start_date' => time(),
                        'use_end_date' => strtotime('+2 day'));
                    mysqld_insert('package_bonus_user', $data);
                    mysqld_insert('bonus_user',$data);
                }
            } else {
                $rank_model = mysqld_select("SELECT * FROM " . table('rank_model')."where rank_level=".intval($_GP['rank_level']) );
                if(!empty($rank_model['rank_level'])) {
                    $rank_model2 = mysqld_select("SELECT * FROM " . table('rank_model')."where rank_level>".$rank_model['rank_level'].' order  by rank_level limit 1' );
                    if(!empty($rank_model2['rank_level'])) {
                        if(intval($rank_model['experience'])<intval($rank_model2['experience'])) {
                            $condition=" and experience<".$rank_model2['experience'];
                        }
                    } else {
                        $condition="";
                    }
                    $member_list = mysqld_selectall("SELECT * FROM " . table('member')."where istemplate=0 and experience>=".intval($rank_model['experience']).$condition );
                    foreach ($member_list as &$member) {
                        $bonus_sn=date("Ymd",time()).$rank_model['type_id'].rand(1000000,9999999);
                        $bonus_user = mysqld_select("SELECT * FROM " . table('package_bonus_user')."where bonus_sn='".$bonus_sn."'" );
                        while(!empty($bonus_user['package_bonus_id'])) {
                            $bonus_sn=date("Ymd",time()).$rank_model['type_id'].rand(1000000,9999999);
                            $bonus_user = mysqld_select("SELECT * FROM " . table('package_bonus_user')."where bonus_sn='".$bonus_sn."'" );
                        }
                        $data = array('package_bonus_id' => $_GP['bonus_id'],
                            'bonus_sn' => $bonus_sn,
                            'openid' => $member['openid'],
                            'package_id' => $bonus['package_id'],
                            'weixin_openid' => $member['weixin_openid'],
                            'eable_days'=>$bonus['eable_days'],
                            'use_start_date' => time(),
                            'use_end_date' => strtotime('+2 day'));
                        mysqld_insert('package_bonus_user', $data);
                    }
                }
            }
            message("发放成功！","refresh","success");
        }
        if (checksubmit('search_phone')) {
            $search_member_phone_list = mysqld_selectall("SELECT * FROM " . table('member')."where istemplate=0 and mobile like :mobile",array(":mobile"=>'%'.$_GP['send_user_tel'].'%'));
        }
        if (checksubmit('search_weixin')) {
            $search_member_weixin_list = mysqld_selectall("SELECT * FROM " . table('member')."where istemplate=0 and weixin_openid like :weixin_openid",array(":weixin_openid"=>'%'.$_GP['send_user_weixin'].'%'));
        }
        $user=$_GP['user'];
        include page('packagesenduserbonus');
        exit;
    }
    if($bonus['bonus_send_type']==1) {
        if(empty($_GP['bonus_id'])) {
            message("无法获取到优惠券信息");
        }
        $category = mysqld_selectall("SELECT * FROM " . table('shop_category') . " where deleted=0 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
        if (!empty($category)) {
            $children = '';
            foreach ($category as $cid => $cate) {
                if (!empty($cate['parentid'])) {
                    $children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
                }
            }
        }
        $condition = '';
        if (!empty($_GP['cate_2'])) {
            $cid = intval($_GP['cate_2']);
            $condition .= " AND ccate = '{$cid}'";
        } elseif (!empty($_GP['cate_1'])) {
            $cid = intval($_GP['cate_1']);
            $condition .= " AND pcate = '{$cid}'";
        }
        $bonus_good_list = mysqld_selectall("SELECT bonus_good.*,shop_goods.title FROM " . table('package_good') . " bonus_good left join " . table('shop_goods') . " shop_goods on shop_goods.id=package_good.good_id WHERE  package_good.bonus_id=:bonus_id",array(":bonus_id"=>intval($_GP['bonus_id'])));
        $goodslist = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  deleted=0 $condition and status=1 and id not in (SELECT good_id FROM " . table('package_good') . " WHERE  bonus_id=:bonus_id)",array(":bonus_id"=>intval($_GP['bonus_id'])));
        if (checksubmit('send_goods')) {
            mysqld_delete('package_good',array('bonus_id'=>intval($_GP['bonus_id'])));
            foreach ($_GP['good_add'] as &$goodid) {
                $bonus_good = mysqld_select("SELECT * FROM " . table('package_good') . " WHERE  bonus_id=:bonus_id and good_id=:good_id",array(":bonus_id"=>intval($_GP['bonus_id']),":good_id"=>intval($goodid)));
                $goods = mysqld_select("SELECT * FROM " . table('shop_goods') . " WHERE  deleted=0 and id=:goodid and status=1",array(":goodid"=>$goodid));
                if(empty($bonus_good['id'])&&!empty($goods['id'])) {
                    mysqld_insert('package_good',array('bonus_id'=>intval($_GP['bonus_id']),'good_id'=>$goodid));
                }
            }
            message("设置成功！","refresh","success");
        }
        include page('packagesendgoodbonus');
        exit;
    }
    if($bonus['bonus_send_type']==3) {
        if(empty($_GP['id'])) {
            message("无法获取到优惠券信息");
        }
        if (checksubmit('submit')) {
            $create_count=intval($_GP['create_count']);
            for ($i=0;$create_count>$i;$i++) {
                $bonus_sn=date("Ymd",time()).$rank_model['type_id'].rand(1000000,9999999);
                $bonus_user = mysqld_select("SELECT * FROM " . table('package_bonus_user')."where bonus_sn='".$bonus_sn."'" );
                while(!empty($bonus_user['bonus_id'])) {
                    $bonus_sn=date("Ymd",time()).$rank_model['type_id'].rand(1000000,9999999);
                    $bonus_user = mysqld_select("SELECT * FROM " . table('package_bonus_user')."where bonus_sn='".$bonus_sn."'" );
                }
                $data = array('package_bonus_id' => $_GP['bonus_id'],
                    'bonus_sn' => $bonus_sn,
                    'openid' => $member['openid'],
                    'package_id' => $bonus['package_id'],
                    'weixin_openid' => $member['weixin_openid'],
                    'eable_days'=>$bonus['eable_days'],
                    'use_start_date' => time(),
                    'use_end_date' => strtotime('+2 day'));
                mysqld_insert('package_bonus_user', $data);
            }
            message("发放成功！",create_url('site', array('name' => 'package','do' => 'package','op'=>'display')),"success");
        }
        include page('packagesendbonus');
        exit;
    }
    header("location:". create_url('site', array('name' => 'package','do' => 'package','op'=>'display')));
}