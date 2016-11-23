<?php
/**
 * Created by PhpStorm.
 * User: yanru02
 * Date: 2016/11/23
 * Time: 14:46
 */
$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
$bonus['id'] = $_GP['id'];
$bonus['thumb'] = $_GP['thumb'];
$bonus['name'] = $_GP['name'];

if ($operation == 'display') {
    include themePage('getbonus');
} elseif ($operation == 'post') {

}