<?php
/**
 * Created by PhpStorm.
 * User: yanru02
 * Date: 2017/2/13
 * Time: 14:25
 */
$member=get_member_account();
$openid =$member['openid'] ;
$weixin_openid = $member['weixin_openid'];
if(empty($_GP['op']) || 'collection'==$_GP['op']){
    $head_thumb = mysqld_select("SELECT thumb FROM " . table('shop_adv'). " WHERE link=:link ",array(":link"=>'group'));
    $list = mysqld_selectall("SELECT * FROM " . table('group')." WHERE isshow=:isshow AND isgroup=:isgroup ", array(":isshow"=>1, "isgroup"=>0));
    include themePage('bangnicai');
}else if('success' == $_GP['op']){
    $head_thumb = mysqld_select("SELECT thumb FROM " . table('shop_adv'). " WHERE link=:link ",array(":link"=>'group'));
    $list = mysqld_selectall("SELECT * FROM " . table('group')." WHERE isshow=:isshow AND isgroup=:isgroup ", array(":isshow"=>0, "isgroup"=>1));
    include themePage('bangnicai');
}