<?php
/**
 * Created by PhpStorm.
 * User: yanru02
 * Date: 2016/11/23
 * Time: 14:46
 */
$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';

if ($operation == 'display') {

    $list = mysqld_selectall("SELECT * FROM " . table('shop_adv') . " WHERE  id = ".$_GP['id']);
    foreach ($list as $adv){
        $bonus['id'] = $adv['id'];
        $bonus['thumb'] = $adv['thumb'];
        $bonus['name'] = $adv['name'];
    }

    include themePage('getbonus');
} elseif ($operation == 'post') {

}