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
if (empty($_GP['op']) || 'collection'==$_GP['op']){
    $head_thumb = mysqld_select("SELECT thumb FROM " . table('shop_adv'). " WHERE link=:link ",array(":link"=>'group'));
    $list = mysqld_selectall("SELECT a.id, a.goodname, a.isshow, a.limittime, a.isgroup, a.goodsn, a.praise, a.thumb, a.sucessthumb,
 a.description, a.express, SUM(a.ispraise) AS ispraise FROM (SELECT hetuantuan_group.*, IF(hetuantuan_group.id=hetuantuan_group_user.goodid,1,0) 
  AS ispraise from hetuantuan_group, hetuantuan_group_user WHERE hetuantuan_group.isshow=:isshow AND hetuantuan_group.isgroup=:isgroup AND 
  hetuantuan_group_user.weixin_openid=:weixin_openid) a GROUP BY a.praise ", array(":isshow"=>1, "isgroup"=>0, ':weixin_openid'=>$weixin_openid));
    if(empty($list)){
        $list = mysqld_selectall("SELECT * FROM " . table('group')." WHERE isshow=:isshow AND isgroup=:isgroup GROUP BY praise ", array(":isshow"=>1, "isgroup"=>0));
    }
    $option = 0;
    include themePage('bangnicai');
}else if ('success' == $_GP['op']){
    $head_thumb = mysqld_select("SELECT thumb FROM " . table('shop_adv'). " WHERE link=:link ",array(":link"=>'group'));
    $list = mysqld_selectall("SELECT * FROM " . table('group')." WHERE isshow=:isshow AND isgroup=:isgroup ", array(":isshow"=>0, "isgroup"=>1));
    $option = 1;
    include themePage('bangnicai');
}else if ('addpraise' == $_GP['op']){
    $isgroup = mysqld_select("SELECT isgroup FROM " . table('group_user') . " WHERE goodid = :goodid AND weixin_openid = :weixin_openid", array(':goodid'=>$_GP['id'], ':weixin_openid'=>$weixin_openid));
    if(!empty($isgroup['isgroup'])){
        $item = mysqld_select("SELECT praise FROM " . table('group') . " WHERE id = :id", array(':id'=>$_GP['id']));
        $item['praise']--;
        mysqld_update('group', array('praise' => $item['praise']), array('id'=>$_GP['id']));
        $isgroup['isgroup'] = 0;
        mysqld_update('group_user', array('isgroup' => 0), array('goodid'=>$_GP['id'], 'weixin_openid'=>$weixin_openid));
    }else if(null != $isgroup['isgroup']){
        $item = mysqld_select("SELECT praise FROM " . table('group') . " WHERE id = :id", array(':id'=>$_GP['id']));
        $item['praise']++;
        mysqld_update('group', array('praise' => $item['praise']), array('id'=>$_GP['id']));
        $isgroup['isgroup'] = 1;
        mysqld_update('group_user', array('isgroup' => 1), array('goodid'=>$_GP['id'], 'weixin_openid'=>$weixin_openid));
    }else{
        $item = mysqld_select("SELECT praise FROM " . table('group') . " WHERE id = :id", array(':id'=>$_GP['id']));
        $item['praise']++;
        mysqld_update('group', array('praise' => $item['praise']), array('id'=>$_GP['id']));
        $isgroup['isgroup'] = 1;
        mysqld_insert('group_user', array('ispraise' => 1, 'goodid'=>$_GP['id'], 'weixin_openid'=>$weixin_openid, 'openid'=>$openid));
    }
    die(json_encode(array("result" => 1, "total" => $item['praise'], 'statue' => $isgroup['isgroup'])));
}