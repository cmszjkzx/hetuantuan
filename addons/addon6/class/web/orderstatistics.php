<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.hetuantuan.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: �ټ�cms <QQ:1987884799> <http://www.hetuantuan.com>
// +----------------------------------------------------------------------
$condition = "";
 $pindex = max(1, intval($_GP['page']));
 $psize = 20;
 $isstatus = intval($_GP['isstatus']);
 if(empty($isstatus))
    {
     $isstatus = 1;
     }
 if(!empty($_GP['start_time']) && !empty($_GP['end_time']))
    {
     $start_time = strtotime($_GP['start_time'] . " 00:00:01");
     $end_time = strtotime($_GP['end_time'] . " 23:59:59");
     }else
    {
     $start_time = strtotime(date('Y-m-01 00:00:01', time()));
     $end_time = strtotime(date('Y-m-t 23:59:59', time()));
    
     }
 $condition = " and t1.createtime>=" . $start_time . " and t1.createtime<=" . $end_time;

 if(!empty($_GP['realname']))
    {
     $realname = $_GP['realname'];
     $condition .= " and t1.realnamestr='" . $realname . "'";
     }
 if(!empty($_GP['addressname']))
    {
     $addressname = $_GP['addressname'];
     $condition .= " and t1.tdrealname='" . $addressname . "'";
     }
 if(!empty($_GP['ordersn']))
    {
     $ordersn = $_GP['ordersn'];
     $condition .= " and t1.ordersn='" . $ordersn . "'";
     }
 if($isstatus == 1)
{
     $conditionOrderStatus = "and orders.status>=2";

}else
 {
     $conditionOrderStatus = "and orders.status>3";
 }
 if(!empty($_GP['orderstatisticsEXP01']))
    {
    
     $psize = 9999;
     $pindex = 1;
     }
 $list = mysqld_selectall("select t1.* from (SELECT orders.status,orders.id,orders.createtime,orders.ordersn,orders.price,orders.dispatchprice,orders.paytype,(orders.address_realname ) tdrealname,(concat(orders.address_province,orders.address_city,orders.address_area,orders.address_address) ) tdaddress,(orders.address_mobile ) tdmobile from " . table('shop_order') . " orders where 1=1 $conditionOrderStatus order by orders.createtime  desc) t1 where 1=1   $condition   LIMIT " . ($pindex - 1) * $psize . ',' . $psize);


 foreach ($list as $id => $displayorder){
     $profit = 0;
     //$list[$id]['ordergoods'] = mysqld_selectall("SELECT (select category.name	from" . table('shop_category') . " category where (0=goods.ccate and category.id=goods.pcate) or (0!=goods.ccate and category.id=goods.ccate) ) as categoryname,goods.thumb,ordersgoods.price,ordersgoods.total,goods.title,ordersgoods.optionname from " . table('shop_order_goods') . " ordersgoods left join " . table('shop_goods') . " goods on goods.id=ordersgoods.goodsid  where  ordersgoods.orderid=:oid order by ordersgoods.createtime  desc ", array(':oid' => $list[$id]['id']));;
     $list[$id]['ordergoods']=mysqld_selectall("SELECT (select category.name	from" . table('shop_category') .
         " category where (0=goods.ccate and category.id=goods.pcate) or (0!=goods.ccate and category.id=goods.ccate) ) 
                as categoryname,goods.thumb,ordersgoods.price,ordersgoods.total,goods.title,ordersgoods.optionname,goods.goodssn,goods.band, 
                if(ordersgoods.optionname is null, goods.productprice, goodsoption.productprice) as productprice from "
         . table('shop_order_goods') . " ordersgoods left join " . table('shop_goods')
         . " goods on goods.id=ordersgoods.goodsid left join ".table('shop_goods_option')
         ."goodsoption on ordersgoods.optionid = goodsoption.id where  ordersgoods.orderid=:oid order by ordersgoods.createtime  desc ",
         array(':oid' => $list[$id]['id']));
     foreach ($list[$id]['ordergoods'] as $good){
         $profit += $good['price']-$good['productprice'];
     }
     $list[$id]['profit'] = $profit;
 }

 $total = mysqld_selectcolumn("select count(t1.id) from (SELECT orders.* from " . table('shop_order') . " orders where 1=1 $conditionOrderStatus order by orders.createtime  desc) t1 where 1=1  $condition  ");
 $pager = pagination($total, $pindex, $psize);

 if(!empty($_GP['orderstatisticsEXP01']))
    {
     $report = "orderstatistics";
    
     require_once 'report.php';
     exit;
     }
 include addons_page('orderstatistics');