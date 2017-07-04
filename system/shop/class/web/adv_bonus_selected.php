<?php
/**
 * Created by PhpStorm.
 * User: yanru02
 * Date: 2016/11/29
 * Time: 20:51
 */
$bonus_selected = random(2);
//2016-11-29-yanru-begin
$adv = mysqld_select("select * from " . table('shop_adv') . " where id=:id  limit 1", array(":id" => $id));
$adv_bonus = array();
if(!empty($adv['hasbonus'])){
    $adv_bonus = explode('-',$adv['hasbonus']);
    array_shift($adv_bonus);
}
$bonuslist = mysqld_selectall("select * from " . table('bonus_type') . " where send_type=0 and deleted=0 and use_start_date<=:use_start_date and use_end_date>=:use_end_date" ,array(":use_start_date"=>time(),":use_end_date"=>time()));
//end
include page('bonus_item');