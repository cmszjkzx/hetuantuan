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

$isgroup = mysqld_select("SELECT isgroup FROM " . table('group_user') . " WHERE goodid = :goodid AND weixin_openid = :weixin_openid", array(':goodid'=>$_GP['id'], ':weixin_openid'=>$weixin_openid));
if(!empty($isgroup['isgroup'])) {
    $status = 1;
}else if(null != $isgroup['isgroup']) {
    $status = 0;
}else{
    $status = -1;
}

if (empty($_GP['op']) || 'collection'==$_GP['op']){
    $head_thumb = mysqld_select("SELECT thumb FROM " . table('shop_adv'). " WHERE link=:link ",array(":link"=>'group'));
    $list = mysqld_selectall("SELECT * FROM " . table('group')." WHERE isshow=:isshow AND isgroup=:isgroup ", array(":isshow"=>1, "isgroup"=>0));
    $option = 0;
    include themePage('bangnicai');
}else if ('success' == $_GP['op']){
    $head_thumb = mysqld_select("SELECT thumb FROM " . table('shop_adv'). " WHERE link=:link ",array(":link"=>'group'));
    $list = mysqld_selectall("SELECT * FROM " . table('group')." WHERE isshow=:isshow AND isgroup=:isgroup ", array(":isshow"=>0, "isgroup"=>1));
    $option = 1;
    include themePage('bangnicai');
}else if ('addpraise' == $_GP['op']){
    if(1 == $status){
        $item = mysqld_select("SELECT praise FROM " . table('group') . " WHERE id = :id", array(':id'=>$_GP['id']));
        $item['praise']--;
        mysqld_update('group', array('praise' => $item['praise']), array('id'=>$_GP['id']));
        $status = 0;
        mysqld_update('group_user', array('isgroup' => 0), array('goodid'=>$_GP['id'], 'weixin_openid'=>$weixin_openid));
    }else if(0 == $status){
        $item = mysqld_select("SELECT praise FROM " . table('group') . " WHERE id = :id", array(':id'=>$_GP['id']));
        $item['praise']++;
        mysqld_update('group', array('praise' => $item['praise']), array('id'=>$_GP['id']));
        $status = 1;
        mysqld_update('group_user', array('isgroup' => 1), array('goodid'=>$_GP['id'], 'weixin_openid'=>$weixin_openid));
    }else{
        $item = mysqld_select("SELECT praise FROM " . table('group') . " WHERE id = :id", array(':id'=>$_GP['id']));
        $item['praise']++;
        mysqld_update('group', array('praise' => $item['praise']), array('id'=>$_GP['id']));
        $status = 1;
        mysqld_insert('group_user', array('isgroup' => 1, 'goodid'=>$_GP['id'], 'weixin_openid'=>$weixin_openid, 'openid'=>$openid));
    }
    die(json_encode(array("result" => 1, "total" => $item['praise'], 'status' => $status)));
}