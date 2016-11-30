<?php
/**
 * Created by PhpStorm.
 * User: yanru02
 * Date: 2016/11/23
 * Time: 14:46
 */
$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
if ( is_use_weixin() ) {
    $settings=globaSetting();

    $dzddes = $settings['shop_description'];

    $dzdtitle = $settings['shop_title'];

    $dzdpic = WEBSITE_ROOT.'/attachment/'.$settings['shop_logo'];

    $shopwap_weixin_share = weixin_share('confirm',array(),$dzdtitle,$dzdpic,$dzddes,$settings);
}
if ( is_use_weixin()) {
    include WEB_ROOT.'/system/common/template/mobile/weixinconfirm.php';
}
if($_GP["follower"]!="nologinby")
{
    //判断用户是否手机登录
    if(is_login_account()==false)
    {
        if(empty($_SESSION["noneedlogin"]))
        {
            tosaveloginfrom();
            header("location:".create_url('mobile',array('name' => 'shopwap','do' => 'login','from'=>'confirm')));
            exit;
        }
    }
}else
{
    $_SESSION["noneedlogin"]=true;
    clearloginfrom();
}
$member=get_member_account();
$openid =$member['openid'] ;
$weixin_openid = $member['weixin_openid'];
if ($operation == 'display') {
    $list = mysqld_selectall("SELECT * FROM " . table('shop_adv') . " WHERE  id = ".$_GP['id']);
    foreach ($list as $adv){
        $adv_bonus = array();
        if(!empty($adv['hasbonus'])){
            $adv_bonus = explode('-',$adv['hasbonus']);
            array_shift($adv_bonus);
        }
        foreach ($adv_bonus as $bonus){
            $bonuslist = mysqld_select("select * from " . table('bonus_type') . " where type_id=:type_id" ,array(":type_id"=>$bonus));
            if(!empty($weixin_openid)){
                $bonus_sn=date("Ymd",time()).$rank_model['type_id'].rand(1000000,9999999);
                $data=array('createtime'=>time(),
                    'openid'=>'',
                    'weixin_openid' => $weixin_openid,
                    'bonus_sn'=>$bonus_sn,
                    'deleted'=>0,
                    'isuse'=>0,
                    'bonus_type_id'=>$bonuslist['type_id']);
                mysqld_insert('bonus_user',$data);
            }else if (!empty($openid)){
                $bonus_sn=date("Ymd",time()).$rank_model['type_id'].rand(1000000,9999999);
                $data=array('createtime'=>time(),
                    'openid'=>$openid,
                    'weixin_openid' => '',
                    'bonus_sn'=>$bonus_sn,
                    'deleted'=>0,
                    'isuse'=>0,
                    'bonus_type_id'=>$bonuslist['type_id']);
                mysqld_insert('bonus_user',$data);
            }
        }
    }
    include themePage('getbonus');
} elseif ($operation == 'post') {

}