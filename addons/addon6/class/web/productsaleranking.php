<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.hetuantuan.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: �ټ�cms <QQ:1987884799> <http://www.hetuantuan.com>
// +----------------------------------------------------------------------

 	$condition="";
if(!empty($_GP['start_time'])&&!empty($_GP['end_time']))
{
  $start_time= strtotime($_GP['start_time']." 00:00:01");
	$end_time= strtotime($_GP['end_time']." 23:59:59");
}else
{
	$start_time= strtotime(date('Y-m-01 00:00:01',time()));
	$end_time= strtotime(date('Y-m-t 23:59:59',time()));
		      						
}

if(empty($_GP['op']) || 'salesmoney' == $_GP['op']) {
    $condition = " and ordergoods.createtime>=" . $start_time . " and ordergoods.createtime<=" . $end_time;
    $list = mysqld_selectall("SELECT goods.*,0 as cpersent,(select sum((ordergoods.price*ordergoods.total)) from  "
        . table('shop_order_goods') .
        " ordergoods left join "
        .table('shop_order').
        " orders on ordergoods.orderid=orders.id where orders.status >=2 and ordergoods.goodsid=goods.id $condition) salesmoney,(select sum(ordergoods.total) from  "
        . table('shop_order_goods') .
        " ordergoods left join "
        .table('shop_order').
        " orders on ordergoods.orderid=orders.id where orders.status >=2 and  ordergoods.goodsid=goods.id $condition) salescount  from "
        . table('shop_goods')
        . " goods order by salesmoney  desc");
}else if('salescount' == $_GP['op']){
    $condition = " and ordergoods.createtime>=" . $start_time . " and ordergoods.createtime<=" . $end_time;
    $list = mysqld_selectall("SELECT goods.*,0 as cpersent,(select sum((ordergoods.price*ordergoods.total)) from  "
        . table('shop_order_goods') .
        " ordergoods left join "
        .table('shop_order').
        " orders on ordergoods.orderid=orders.id where orders.status >=2 and ordergoods.goodsid=goods.id $condition) salesmoney,(select sum(ordergoods.total) from  "
        . table('shop_order_goods') .
        " ordergoods left join "
        .table('shop_order').
        " orders on ordergoods.orderid=orders.id where orders.status >=2 and  ordergoods.goodsid=goods.id $condition) salescount  from "
        . table('shop_goods')
        . " goods order by salescount  desc");
}


if(!empty($_GP['productsalerankingEXP01']))
{
 	$report="productsaleranking";
   require_once 'report.php';
    exit;
}
                   	 
		               		
 include addons_page('productsaleranking');