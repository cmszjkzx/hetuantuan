<?php

$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
 	  										
$hasaddon16=false;
$normal_order_list = array();
$express_order_list = array();
$addon16=mysqld_select("SELECT * FROM " . table('modules') . " WHERE name = 'addon16' limit 1");
if(!empty($addon16['name']))
{
    if(is_file(ADDONS_ROOT.'addon16/key.php'))
    {
        $normal_order_list = mysqld_selectall("SELECT * FROM " . table('addon16_printer') . " WHERE  printertype=0 order by isdefault desc");
        $express_order_list = mysqld_selectall("SELECT * FROM " . table('addon16_printer') . " WHERE  printertype=1 order by isdefault desc");
        $hasaddon16=true;
    }
}
if ($operation == 'display')
{
    $pindex = max(1, intval($_GP['page']));
    $psize = 20;
    $status = !isset($_GP['status']) ? 1 : $_GP['status'];
    $sendtype = !isset($_GP['sendtype']) ? 0 : $_GP['sendtype'];
    $condition = '';
    $band_condition = '';//2016-12-25-yanru
    $param_ordersn=$_GP['ordersn'];
    //shop_order字段
    if (!empty($_GP['ordersn']))
    {
        $condition .= " AND ordersn LIKE '%{$_GP['ordersn']}%'";
        $band_condition .= " AND shoporders.ordersn LIKE '%{$_GP['ordersn']}%'";//2016-12-25-yanru
    }

    if (!empty($_GP['paytype']))
    {
        $condition .= " AND paytypecode ='".$_GP['paytype']."'";
        $band_condition .= " AND shoporders.paytypecode ='".$_GP['paytype']."'";//2016-12-25-yanru
    }
    if (!empty($_GP['dispatch']))
    {
        $condition .= " AND dispatch =".intval($_GP['dispatch']);
        $band_condition .= " AND shoporders.dispatch =".intval($_GP['dispatch']);//2016-12-25-yanru
    }
    if (!empty($_GP['endtime']))
    {
        $condition .= " AND createtime  <= ". strtotime($_GP['endtime']);
        $band_condition .= " AND shoporders.createtime  <= ". strtotime($_GP['endtime']);//2016-12-25-yanru
    }
    if (!empty($_GP['begintime']))
    {
        $condition .= " AND createtime  >= ". strtotime($_GP['begintime']);
        $band_condition .= " AND shoporders.createtime  >= ". strtotime($_GP['begintime']);//2016-12-25-yanru
    }
    if (!empty($_GP['address_realname']))
    {
        $condition .= " AND address_realname  LIKE '%{$_GP['address_realname']}%'";
        $band_condition .= " AND shoporders.address_realname  LIKE '%{$_GP['address_realname']}%'";//2016-12-25-yanru
    }
    if (!empty($_GP['address_mobile']))
    {
        $condition .= " AND address_mobile  LIKE '%{$_GP['address_mobile']}%'";
        $band_condition .= " AND shoporders.address_mobile  LIKE '%{$_GP['address_mobile']}%'";//2016-12-25-yanru
    }
    //2016-12-16-yanru-新增状态9是已收货，状态4和5的集合
    if ($status != '-99' && $status != 9 && $status != 10 && $status != 11 && $status != 12) {
        $condition .= " AND status = '" . intval($status) . "'";
        $band_condition .= " AND shoporders.status = '" . intval($status) . "'";//2016-12-25-yanru
    }
    if ($status == '9'){
        $condition .= " and ( status = 4 or status = 5) ";
        $band_condition .= " and ( shoporders.status = 4 or shoporders.status = 5) ";//2016-12-25-yanru
    }
    //2016-12-25-yanru-新增状态10是表示需要更新快递信息的订单，状态2和3的集合
    if ($status == '10'){
        $condition .= " and ( status >= 2 and status <= 5) ";
        $band_condition .= " and ( shoporders.status >= 2 and shoporders.status <= 5) ";//2016-12-25-yanru
    }
    if ($status == '3') {
        $condition .= " and ( status = 3 or status = -5 or status = -6) ";
        $band_condition .= " and ( shoporders.status = 3 or shoporders.status = -5 or shoporders.status = -6) ";//2016-12-25-yanru
    }
    //当是商家登录时判断对应商家的商品是否发货,其中5为待发货,6为待收货
    if (($status == '11' || $status == '12') && (empty($_CMS[WEB_SESSION_ACCOUNT]['is_admin'])||!empty($_GP['bandmanage']))){
        if($status == '11')
            $band_condition .= " AND (shoporders.status = 2 OR (shoporders.status = 3 AND ordergoods.status = 11)) ";//2017-1-3-yanru
        if($status == '12')
            $band_condition .= " AND shoporders.status = 3 AND ordergoods.status = 12 ";//2017-1-3-yanru
    }
    //2017-03-20-yanru-商家全部订单中不包含关闭和超时等非正常状态
    if($status == '-99'){
        $band_condition .= " AND shoporders.status >= 0 AND shoporders.status != 1 ";
    }
    //2017-1-5-yanru-新增通过微信名查询
    if(!empty($_GP['weixin_nickname'])){
        $get_weixin_openid = mysqld_select("select weixin_openid from ".table('weixin_wxfans')." where nickname like '%{$_GP['weixin_nickname']}%'");
        $condition .= " AND weixin_openid ='".$get_weixin_openid['weixin_openid']."' ";
        $band_condition .= " AND shoporders.weixin_openid ='".$get_weixin_openid['weixin_openid']."' ";//2016-12-25-yanru
    }
    //2017-1-5-yanru-优化代码数据查询

    //end
    $dispatchs = mysqld_selectall("SELECT * FROM " . table('shop_dispatch') );
    $dispatchdata=array();
    if(is_array($dispatchs))
    {
        foreach($dispatchs as $disitem)
        {
            $dispatchdata[$disitem['id']]=$disitem;
        }
    }
    $selectCondition="LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
    $orderby = " DESC ";
    if (!empty($_GP['report']) || !empty($_GP['getexcel']))
    {
        $selectCondition="";
        $orderby = " ASC ";
    }

    //$list = mysqld_selectall("SELECT * FROM " . table('shop_order') . " WHERE 1=1 $condition ORDER BY  createtime DESC ".$selectCondition);
    //2016-12-25-yanru-新增根据商品品牌获取订单
    if(!empty($_CMS[WEB_SESSION_ACCOUNT]['is_admin'])){
        if(empty($_GP['band']) && empty($_GP['bandmanage'])){
            $list = mysqld_selectall("SELECT * FROM " . table('shop_order') . " WHERE 1=1 $condition ORDER BY createtime ".$orderby.$selectCondition);
            $total = mysqld_selectcolumn("SELECT COUNT(*) FROM " . table('shop_order') . " WHERE 1=1 $condition ORDER BY createtime ".$orderby);
        }else{
            $bandname = empty($_GP['band'])?$_GP['bandmanage']:$_GP['band'];
            if("其他" == $bandname){
                $list = mysqld_selectall("SELECT shoporders.*, SUM(ordergoods.price) AS optionsprice, ordergoods.status AS optionstatus FROM " . table('shop_order') . " shoporders LEFT JOIN ".table('shop_order_goods').
                    " ordergoods ON ordergoods.orderid = shoporders.id LEFT JOIN ".table('shop_goods')." goods ON goods.id = ordergoods.goodsid"
                    ." WHERE goods.band=:band $band_condition GROUP BY shoporders.id ORDER BY shoporders.createtime ".$orderby.$selectCondition, array(':band' => ''));
                $total = mysqld_query("SELECT shoporders.*, SUM(ordergoods.price) AS optionsprice, ordergoods.status AS optionstatus FROM " . table('shop_order') . " shoporders LEFT JOIN ".table('shop_order_goods').
                    " ordergoods ON ordergoods.orderid = shoporders.id LEFT JOIN ".table('shop_goods')." goods ON goods.id = ordergoods.goodsid"
                    ." WHERE goods.band=:band $band_condition GROUP BY shoporders.id ORDER BY shoporders.createtime ".$orderby, array(':band' => ''));
            }
            else{
                $list = mysqld_selectall("SELECT shoporders.*, SUM(ordergoods.price) AS optionsprice, ordergoods.status AS optionstatus FROM " . table('shop_order') . " shoporders LEFT JOIN ".table('shop_order_goods').
                    " ordergoods ON ordergoods.orderid = shoporders.id LEFT JOIN ".table('shop_goods')." goods ON goods.id = ordergoods.goodsid"
                    ." WHERE goods.band=:band $band_condition GROUP BY shoporders.id ORDER BY shoporders.createtime ".$orderby.$selectCondition, array(':band' => $bandname));
                $total = mysqld_query("SELECT shoporders.*, SUM(ordergoods.price) AS optionsprice, ordergoods.status AS optionstatus FROM " . table('shop_order') . " shoporders LEFT JOIN ".table('shop_order_goods').
                    " ordergoods ON ordergoods.orderid = shoporders.id LEFT JOIN ".table('shop_goods')." goods ON goods.id = ordergoods.goodsid"
                    ." WHERE goods.band=:band $band_condition GROUP BY shoporders.id ORDER BY shoporders.createtime ".$orderby, array(':band' => $bandname));
            }
        }
    }else{
        $list = mysqld_selectall("SELECT shoporders.*, SUM(ordergoods.price) AS optionsprice, ordergoods.status AS optionstatus FROM " . table('shop_order') . " shoporders LEFT JOIN ".table('shop_order_goods').
            " ordergoods ON ordergoods.orderid = shoporders.id LEFT JOIN ".table('shop_goods')." goods ON goods.id = ordergoods.goodsid"
            ." WHERE goods.band=:band $band_condition GROUP BY shoporders.id ORDER BY shoporders.createtime ".$orderby.$selectCondition, array(':band' => $_CMS[WEB_SESSION_ACCOUNT]['groupName']));
        $total = mysqld_query("SELECT shoporders.*, SUM(ordergoods.price) AS optionsprice, ordergoods.status AS optionstatus FROM " . table('shop_order') . " shoporders LEFT JOIN ".table('shop_order_goods').
            " ordergoods ON ordergoods.orderid = shoporders.id LEFT JOIN ".table('shop_goods')." goods ON goods.id = ordergoods.goodsid"
            ." WHERE goods.band=:band $band_condition GROUP BY shoporders.id ORDER BY shoporders.createtime ".$orderby, array(':band' => $_CMS[WEB_SESSION_ACCOUNT]['groupName']));
    }

    if(!empty($list)){
        foreach ($list as &$temp){
            if($temp['status'] == 0 && 30*60 < (time()-$temp['createtime'])){
                $temp['status'] = 1;
                mysqld_update("shop_order", array("status" => 1), array("id" => $temp['id']));
            }
        }
    }

    //$total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_order') . " WHERE 1=1  $condition");
    $pager = pagination($total, $pindex, $psize);
    if(!empty($list)) {
        foreach ($list as $id => $item) {
            if (!empty($item['openid'])) {
                $list[$id]['isguest'] = mysqld_selectcolumn("SELECT istemplate from " . table('member') . " where  openid=:openid ", array(':openid' => $item['openid']));
            }
        }
    }

    if (!empty($_GP['report']))
    {
        foreach ( $list as $id => $item)
        {
            if(!empty($_CMS[WEB_SESSION_ACCOUNT]['is_admin'])){
                if(empty($_GP['band']) && empty($_GP['bandmanage'])){
                    $list[$id]['ordergoods']=mysqld_selectall("SELECT (select category.name	from" . table('shop_category') .
                        " category where (0=goods.ccate and category.id=goods.pcate) or (0!=goods.ccate and category.id=goods.ccate) ) 
                as categoryname,goods.thumb,ordersgoods.price,ordersgoods.total,goods.title,ordersgoods.optionname,goods.goodssn,goods.band, 
                ordersgoods.goodsid,ordersgoods.optionid,if(ordersgoods.optionname is null, goods.productprice, goodsoption.productprice) as productprice from "
                        . table('shop_order_goods') . " ordersgoods left join " . table('shop_goods')
                        . " goods on goods.id=ordersgoods.goodsid left join ".table('shop_goods_option')
                        ."goodsoption on ordersgoods.optionid = goodsoption.id where  ordersgoods.orderid=:oid order by ordersgoods.createtime desc ",
                        array(':oid' => $item['id']));
                } else{
                    $bandname = empty($_GP['band'])?$_GP['bandmanage']:$_GP['band'];
                    if("其他"==$bandname){
                        $list[$id]['ordergoods']=mysqld_selectall("SELECT (select category.name	from" . table('shop_category') .
                            " category where (0=goods.ccate and category.id=goods.pcate) or (0!=goods.ccate and category.id=goods.ccate) ) 
                as categoryname,goods.thumb,ordersgoods.price,ordersgoods.total,goods.title,ordersgoods.optionname,goods.goodssn,goods.band, 
                ordersgoods.goodsid,ordersgoods.optionid,if(ordersgoods.optionname is null, goods.productprice, goodsoption.productprice) as productprice from "
                            . table('shop_order_goods') . " ordersgoods left join " . table('shop_goods')
                            . " goods on goods.id=ordersgoods.goodsid left join ".table('shop_goods_option')
                            ."goodsoption on ordersgoods.optionid = goodsoption.id where  ordersgoods.orderid=:oid and goods.band='' order by ordersgoods.createtime desc ",
                            array(':oid' => $item['id']));
                    }else{
                        $list[$id]['ordergoods']=mysqld_selectall("SELECT (select category.name	from" . table('shop_category') .
                            " category where (0=goods.ccate and category.id=goods.pcate) or (0!=goods.ccate and category.id=goods.ccate) ) 
                as categoryname,goods.thumb,ordersgoods.price,ordersgoods.total,goods.title,ordersgoods.optionname,goods.goodssn,goods.band, 
                ordersgoods.goodsid,ordersgoods.optionid,if(ordersgoods.optionname is null, goods.productprice, goodsoption.productprice) as productprice from "
                            . table('shop_order_goods') . " ordersgoods left join " . table('shop_goods')
                            . " goods on goods.id=ordersgoods.goodsid left join ".table('shop_goods_option')
                            ."goodsoption on ordersgoods.optionid = goodsoption.id where  ordersgoods.orderid=:oid and goods.band=:band order by ordersgoods.createtime desc ",
                            array(':oid' => $item['id'], ':band' => $bandname));
                    }
                }
            }else{
                $list[$id]['ordergoods']=mysqld_selectall("SELECT (select category.name	from" . table('shop_category') .
                    " category where (0=goods.ccate and category.id=goods.pcate) or (0!=goods.ccate and category.id=goods.ccate) ) 
                as categoryname,goods.thumb,ordersgoods.price,ordersgoods.total,goods.title,ordersgoods.optionname,goods.goodssn,goods.band, 
                ordersgoods.goodsid,ordersgoods.optionid,if(ordersgoods.optionname is null, goods.productprice, goodsoption.productprice) as productprice from "
                    . table('shop_order_goods') . " ordersgoods left join " . table('shop_goods')
                    . " goods on goods.id=ordersgoods.goodsid left join ".table('shop_goods_option')
                    ."goodsoption on ordersgoods.optionid = goodsoption.id where  ordersgoods.orderid=:oid and goods.band=:band order by ordersgoods.createtime desc ",
                    array(':oid' => $item['id'], ':band' => $_CMS[WEB_SESSION_ACCOUNT]['groupName']));
            }


            //2016-12-26-yanru-begin-导出的时候加上订单的发货快递信息
            $temp_expresscom = explode(";", $item['expresscom']);
            array_pop($temp_expresscom);
            $item_expresscom = array();
            foreach ($temp_expresscom as $goods_expresscom){
                $item_expresscom[] = explode("_", $goods_expresscom);
            }

            $temp_expresssn = explode(";", $item['expresssn']);
            array_pop($temp_expresssn);
            $item_expresssn = array();
            foreach ($temp_expresssn as $goods_expresssn){
                $item_expresssn[] = explode("_", $goods_expresssn);
            }

            $temp_express = explode(";", $item['express']);
            array_pop($temp_express);
            $item_express = array();
            foreach ($temp_express as $goods_express){
                $item_express[] = explode("_", $goods_express);
            }

            for($i = 0; $i < count($item_express); $i++){
                for($j = 0; $j < count($list[$id]['ordergoods']); $j++){
                    //$item_order_express[] = array("goodssn" => $item_express[$i][0], "expresscom" => $item_expresscom[$i][1], "expresssn" => $item_expresssn[$i][1], "express" => $item_express[$i][1]);
                    if(strpos($item_express[$i][0], "@")){
                        $temp_goodoption = explode("@", $item_express[$i][0]);
                        if($list[$id]['ordergoods'][$j]['goodsid']==$temp_goodoption[0] && $list[$id]['ordergoods'][$j]['optionid']==$temp_goodoption[1]){
                            $list[$id]['ordergoods'][$j]['expresscom'] = $item_expresscom[$i][1];
                            $list[$id]['ordergoods'][$j]['expresssn'] = $item_expresssn[$i][1];
                            $list[$id]['ordergoods'][$j]['express'] = $item_express[$i][1];
                        }
                        //$item_order_express[] = array("goodssn"=>"", "goodsid"=>$temp_goodoption[0], "optionid"=>$temp_goodoption[1], "expresscom"=>$item_expresscom[$i][1], "expresssn"=>$item_expresssn[$i][1], "express"=>$item_express[$i][1]);
                    }else{
                        if($list[$id]['ordergoods'][$j]['optionname']==$item_express[$i][0]||"inserttemp"==$item_express[$i][0]){
                            $list[$id]['ordergoods'][$j]['expresscom'] = $item_expresscom[$i][1];
                            $list[$id]['ordergoods'][$j]['expresssn'] = $item_expresssn[$i][1];
                            $list[$id]['ordergoods'][$j]['express'] = $item_express[$i][1];
                        }
                    }
                }
            }
            //end
        }
        $report='orderreport';
        require_once 'report.php';
        exit;
    }
    //2016-12-2-yanru-begin-expressorder
    if (!empty($_GP['getexcel']))
    {
        if (!empty($_FILES['expressorder']['tmp_name'])) {
            $upload = express_order_upload($_FILES['expressorder']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
        }else{
            message("请导入物流信息文件！");
        }
//        $order_list = mysqld_selectall("SELECT * FROM " . table('shop_order') . " WHERE status = 2 ORDER BY  createtime DESC ");
//        foreach ( $order_list as $id => $item)
//        {
//            $order_list[$id]['ordergoods']=mysqld_selectall("SELECT (select category.name	from" . table('shop_category') . " category where (0=goods.ccate and category.id=goods.pcate) or (0!=goods.ccate and category.id=goods.ccate) ) as categoryname,goods.thumb,ordersgoods.price,ordersgoods.total,goods.title,ordersgoods.optionname,goods.goodssn from " . table('shop_order_goods') . " ordersgoods left join " . table('shop_goods') . " goods on goods.id=ordersgoods.goodsid  where  ordersgoods.orderid=:oid order by ordersgoods.createtime  desc ",array(':oid' => $item['id']));;
//        }
        foreach ( $list as $id => $item)
        {
            //2016-12-26-yanru-begin-每次更新物流信息，修改存储信息，现在存储订单规格编号及对应的商品编号
            $list[$id]['ordergoods']=mysqld_selectall("SELECT (select category.name	from" . table('shop_category') . " category where (0=goods.ccate and category.id=goods.pcate) or (0!=goods.ccate and category.id=goods.ccate) ) as categoryname,goods.thumb,ordersgoods.price,ordersgoods.total,goods.title,ordersgoods.optionname,goods.goodssn,ordersgoods.goodsid, ordersgoods.optionid from " . table('shop_order_goods') . " ordersgoods left join " . table('shop_goods') . " goods on goods.id=ordersgoods.goodsid  where  ordersgoods.orderid=:oid order by ordersgoods.createtime  desc ",array(':oid' => $item['id']));
            //end
        }
        require_once 'readexcle.php';
        exit;
    }
    //2016-12-25-yanru-新增获取所有商品品牌信息
    $bands = mysqld_selectall("SELECT distinct(band) FROM ".table('shop_goods'));
    for($i=0; $i < count($bands); $i++){
        if(empty($bands[$i]['band']))
            $bands[$i]['band'] = "其他";
    }
    //end
    $payments = mysqld_selectall("SELECT * FROM " . table('payment') . " WHERE enabled = 1");
    $hasaddon11=false;
    $addon11=mysqld_select("SELECT * FROM " . table('modules') . " WHERE name = 'addon11' limit 1");
    if(!empty($addon11['name']))
    {
        if(is_file(ADDONS_ROOT.'addon11/key.php'))
        {
            $hasaddon11=true;
        }
    }
    include page('order_list');
}
if ($operation == 'detail')
{
    $orderid=intval($_GP['id']);
    $order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id=:id",array(":id"=>$orderid));
    if($order['hasbonus'])
    {
        $bonuslist = mysqld_selectall("SELECT bonus_user.*,bonus_type.type_name FROM " . table('bonus_user') . " bonus_user left join  " . table('bonus_type') . " bonus_type on bonus_type.type_id=bonus_user.bonus_type_id WHERE bonus_user.order_id=:order_id",array(":order_id"=>$orderid));
    }
    $dispatchlist = mysqld_selectall("SELECT * FROM " . table('dispatch')." where sendtype=0 and enabled = 1" );
    $payments = mysqld_selectall("SELECT * FROM " . table('payment') . " WHERE enabled = 1");
    $dispatchs = mysqld_selectall("SELECT * FROM " . table('shop_dispatch') );
    $dispatchdata=array();
    if(is_array($dispatchs))
    {
        foreach($dispatchs as $disitem)
        {
            $dispatchdata[$disitem['id']]=$disitem;
        }
    }
    //2017-1-9-yanru-begin-后台订单状态优化
    if(!empty($_CMS[WEB_SESSION_ACCOUNT]['is_admin'])){
        if(empty($_GP['band']) && empty($_GP['bandmanage'])){
            $goods = mysqld_selectall("SELECT o.id,o.total, g.title, g.status,g.thumb, g.goodssn,g.productsn,g.marketprice,o.total,g.type,o.optionname,o.optionid,o.goodsid,o.price as orderprice,o.status as optionstatus FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.goodsid=g.id "
                . " WHERE o.orderid='{$orderid}'");
        }else{
            $bandname = empty($_GP['band'])?$_GP['bandmanage']:$_GP['band'];
            if("其他"==$bandname){
                $goods = mysqld_selectall("SELECT o.id,o.total, g.title, g.status,g.thumb, g.goodssn,g.productsn,g.marketprice,o.total,g.type,o.optionname,o.optionid,o.goodsid,o.price as orderprice,o.status as optionstatus FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.goodsid=g.id "
                    . " WHERE o.orderid='{$orderid}' AND g.band=''");
            }else{
                $goods = mysqld_selectall("SELECT o.id,o.total, g.title, g.status,g.thumb, g.goodssn,g.productsn,g.marketprice,o.total,g.type,o.optionname,o.optionid,o.goodsid,o.price as orderprice,o.status as optionstatus FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.goodsid=g.id "
                    . " WHERE o.orderid='{$orderid}' AND g.band='{$bandname}'");
            }

        }
    }else{
        $goods = mysqld_selectall("SELECT o.id,o.total, g.title, g.status,g.thumb, g.goodssn,g.productsn,g.marketprice,o.total,g.type,o.optionname,o.optionid,o.goodsid,o.price as orderprice,o.status as optionstatus FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.goodsid=g.id "
            . " WHERE o.orderid='{$orderid}' AND g.band='{$_CMS[WEB_SESSION_ACCOUNT]['groupName']}'");
    }

    //2017-03-20-yanru-修改商家端显示订单金额不对BUG
    if(is_array($goods) && !empty($goods)){
        $order['price'] = 0;
        for($g=0; $g<=count($goods); $g++){
            $order['price'] += $goods[$g]['total'] * $goods[$g]['orderprice'];
        }
    }
    //end

    //2016-12-15-yanru-begin-后台订单状态查询
    $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id", array(':id' => $orderid));
    if(($item['status'] >= 3 || $item['status'] == -7)&& empty($_GP['confirmsend'])){
        //2016-12-4-yanru-begin
        $temp_expresscom = explode(";", $item['expresscom']);
        array_pop($temp_expresscom);
        $item_expresscom = array();
        foreach ($temp_expresscom as $goods_expresscom){
            $item_expresscom[] = explode("_", $goods_expresscom);
        }

        $temp_expresssn = explode(";", $item['expresssn']);
        array_pop($temp_expresssn);
        $item_expresssn = array();
        foreach ($temp_expresssn as $goods_expresssn){
            $item_expresssn[] = explode("_", $goods_expresssn);
        }

        $temp_express = explode(";", $item['express']);
        array_pop($temp_express);
        $item_express = array();
        foreach ($temp_express as $goods_express){
            $item_express[] = explode("_", $goods_express);
        }
//
//        $item_order_express = array();
//        for($i = 0; $i < count($item_express); $i++){
//            if(!empty($item_order_express)){
//                foreach ($item_order_express as $temp_order_express){
//                    if(
//                        0 != strcmp($item_express[$i][0], $temp_order_express['goodssn']) &&
//                        (
//                            (0 != strcmp($item_express[$i][1], $temp_order_express['express'])) ||
//                            (0 != strcmp($item_expresssn[$i][1], $temp_order_express['expresssn']) && 0 == strcmp($item_express[$i][1], $temp_order_express['express']))
//                        )
//                    ){
//                        $item_order_express[] = array("goodssn"=>$item_express[$i][0], "expresscom"=>$item_expresscom[$i][1], "expresssn"=>$item_expresssn[$i][1], "express"=>$item_express[$i][1]);
//                    }
//                }
//            }else {
//                $item_order_express[] = array("goodssn" => $item_express[$i][0], "expresscom" => $item_expresscom[$i][1], "expresssn" => $item_expresssn[$i][1], "express" => $item_express[$i][1]);
//            }
//        }
        if(!empty($item_express)){
            for($i = 0; $i < count($item_express); $i++){
                for($j = 0; $j < count($goods); $j++){
                    if(strpos($item_express[$i][0], "@")){
                        $temp_goodoption = explode("@", $item_express[$i][0]);
                        if($goods[$j]['goodsid']==$temp_goodoption[0] && $goods[$j]['optionid']==$temp_goodoption[1]){
                            $goods[$j]['expresscom'] = $item_expresscom[$i][1];
                            $goods[$j]['expresssn'] = $item_expresssn[$i][1];
                            $goods[$j]['express'] = $item_express[$i][1];
                        }
                    }else{
                        if($goods[$j]['optionname']==$item_express[$i][0]||"inserttemp"==$item_express[$i][0]){
                            $goods[$j]['expresscom'] = $item_expresscom[$i][1];
                            $goods[$j]['expresssn'] = $item_expresssn[$i][1];
                            $goods[$j]['express'] = $item_express[$i][1];
                        }
                    }
                }
            }
        }
        //end
    }
    //end
    $order['goods'] = $goods;

    if (checksubmit('confrimpay'))
    {
        mysqld_update('shop_order', array('status' => 2,'remark'=>$_GP['remark']), array('id' => $orderid));
        require(WEB_ROOT.'/system/common/extends/class/shop/class/web/order_5.php');
        message('确认订单付款操作成功！', refresh(), 'success');
    }
    if (checksubmit('confirmsend'))
    {
        if ($_GP['express']!="-1" && empty($_GP['expresssn']))
        {
            message('请输入快递单号！');
        }
        $express=$_GP['express'];
        if ($express=="-1")
        {
            $express="";
        }
        if($order['paytypecode']=='bank'||$order['paytypecode']=='delivery')
        {
            updateOrderStock($orderid);
        }
//        mysqld_update('shop_order', array(
//            'status' => 3,
//            'express' => $express,
//            'expresscom' => $_GP['expresscom'],
//            'expresssn' => $_GP['expresssn'],
//            'remark'=>$_GP['remark']),
//            array('id' => $orderid));
        foreach ($goods as $good){
            $temp_goods = $good['goodsid']."@".$good['optionid'];
            $temp_goods_expresscom = $temp_goods."_".$_GP['expresscom'].";";
            $temp_goods_expresssn = $temp_goods."_".$_GP['expresssn'].";";
            $temp_goods_express = $temp_goods."_".$express.";";
            //需要比较下是用strpos还是用stristr
            if(stristr($order['expresssn'], $temp_goods)==false && stristr($order['express'], $temp_goods)==false){
                $temp_goods_expresscom = $order['expresscom'].$temp_goods_expresscom;
                $temp_goods_expresssn = $order['expresssn'].$temp_goods_expresssn;
                $temp_goods_express = $order['express'].$temp_goods_express;
                mysqld_update('shop_order_goods', array(
                    'status' => 12,
                    'express' => $express,
                    'expresscom' => $_GP['expresscom'],
                    'expresssn' => $_GP['expresssn'],),
                    array('orderid' => $orderid, 'goodsid'=>$good['goodsid']));

                $notice = array(
                    "touser" => $order['weixin_openid'],
                    "template_id" => "A-pOebjfRNtuzGSqEVnGwgtjk1Hqt3G9GOpavMVHzb0",
                );
                $first = array(
                    "value" => "您的订单物流信息已更新！",
                    "color" => "#173177"
                );
                $keyword1 = array(
                    "value" => $good['title'].":".$good['optionname'],
                    "color" => "#173177"
                );
                $keyword2 = array(
                    "value" => $order['ordersn'],
                    "color" => "#173177"
                );
                $keyword3 = array(
                    "value" => "中国移动和团团",
                    "color" => "#173177"
                );
                $remark = array(
                    "value" => "满意您再来哟！",
                    "color" => "#173177"
                );
                $noticeDat = array(
                    "first" => $first,
                    "keyword1" => $keyword1,
                    "keyword2" => $keyword2,
                    "keyword3" => $keyword3,
                    "remark" => $remark
                );
                $notice["data"] = $noticeDat;
                $dat = json_encode($notice);
                $dat = urldecode($dat);
                $token = get_weixin_token();
                $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$token}";
                $content = http_post($url, $dat);

                $order_update = array(
                    "status" => 3,
                    "expresscom" => $temp_goods_expresscom,
                    "expresssn" => $temp_goods_expresssn,
                    "express" => $temp_goods_express
                );
                mysqld_update("shop_order", $order_update, array("id" => $order['id']));
            }else{
                $expresscom_begin = strpos($order['expresscom'], $temp_goods);
                $expresscom_end = strpos(substr($order['expresscom'], $expresscom_begin), ";");
                $expresscom_old = substr($order['expresscom'], $expresscom_begin, $expresscom_end+1);
                $expresscom_change = str_ireplace($expresscom_old,$temp_goods_expresscom,$order['expresscom']);

                $expresssn_begin = strpos($order['expresssn'], $temp_goods);
                $expresssn_end = strpos(substr($order['expresssn'], $expresssn_begin), ";");
                $expresssn_old = substr($order['expresssn'], $expresssn_begin, $expresssn_end+1);
                $expresssn_change = str_ireplace($expresssn_old,$temp_goods_expresssn,$order['expresssn']);

                $express_begin = strpos($order['express'], $temp_goods);
                $express_end = strpos(substr($order['express'], $express_begin), ";");
                $express_old = substr($order['express'], $express_begin, $express_end+1);
                $express_change = str_ireplace($express_old,$temp_goods_express,$order['express']);

                $order_update = array(
                    "status" => 3,
                    "expresscom" => $expresscom_change,
                    "expresssn" => $expresssn_change,
                    "express" => $express_change
                );
                mysqld_update("shop_order", $order_update, array("id" => $order['id']));

                mysqld_update('shop_order_goods', array(
                    'status' => 12,
                    'express' => $express,
                    'expresscom' => $_GP['expresscom'],
                    'expresssn' => $_GP['expresssn'],),
                    array('orderid' => $orderid, 'goodsid'=>$good['goodsid']));
            }
        }

        require(WEB_ROOT.'/system/common/extends/class/shop/class/web/order_4.php');
        message('发货操作成功！', refresh(), 'success');
    }
    if (checksubmit('cancelsend'))
    {
        if($order['paytypecode']=='bank'||$order['paytypecode']=='delivery')
        {
            updateOrderStock($orderid,false);
        }
        mysqld_update('shop_order',
            array('status' => 2,
                'remark'=>$_GP['remark']),
            array('id' => $orderid));
        message('取消发货操作成功！', refresh(), 'success');
    }
    if (checksubmit('cancelreturn'))
    {
        $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id", array(':id' => $orderid));
        $ostatus=3;
        if($item['status']==-2)
        {
            $ostatus=1;
        }
        if($item['status']==-3)
        {
            $ostatus=3;
        }
        if($item['status']==-4)
        {
            $ostatus=3;
        }
        mysqld_update('shop_order',
            array('status' => $ostatus,
                'updatetime'=>time()),
            array('id' => $orderid));
        if($item['status']==-2)
        {
            require(WEB_ROOT.'/system/common/extends/class/shop/class/web/order_3.php');
        }
        message('退回操作成功！', refresh(), 'success');
    }
    if (checksubmit('open'))
    {
        mysqld_update('shop_order', array('status' => 0, 'remark' => $_GP['remark']), array('id' => $orderid));
        message('开启订单操作成功！', referer(), 'success');
    }
    if (checksubmit('close'))
    {
        mysqld_update('shop_order', array('status' => -1,'remark'=>$_GP['remark']), array('id' => $orderid));
        message('订单关闭操作成功！', refresh(), 'success');
    }
    if (checksubmit('finish'))
    {
        if (empty($order['isrest']))
        {
            $this->setOrderCredit($order['openid'],$orderid,true,'订单:'.$order['ordersn'].'完成新增积分');
        }
        mysqld_update('shop_order', array('status' => 3, 'remark' => $_GP['remark'],'updatetime'=>time()), array('id' => $orderid));

        require(WEB_ROOT.'/system/common/extends/class/shop/class/web/order_1.php');
        message('订单操作成功！', refresh(), 'success');
    }
    if (checksubmit('returnpay'))
    {
        if($order['paytype']==3)
        {
            message('货到付款订单不能进行退款操作!', refresh(), 'error');
        }
        mysqld_update('shop_order', array('status' => -6,'remark'=>$_GP['remark']), array('id' => $orderid));
        $this->setOrderStock($orderid, false);
        member_gold($order['openid'],$order['price'],'addgold','订单:'.$order['ordersn'].'退款返还余额');
        require(WEB_ROOT.'/system/common/extends/class/shop/class/web/order_2.php');
        message('退款操作成功！', refresh(), 'success');
    }
            
    if (checksubmit('returngood'))
    {
        mysqld_update('shop_order', array('status' => -5,'remark'=>$_GP['remark']), array('id' => $orderid));
        $this->setOrderStock($orderid, false);
        $this->setOrderCredit($order['openid'],$orderid,false,'订单:'.$order['ordersn'].'退货扣除积分');
        member_gold($order['openid'],$order['price'],'addgold','订单:'.$order['ordersn'].'退货返还余额');
        message('退货操作成功！', refresh(), 'success');
    }
    $weixin_wxfans = mysqld_selectall("SELECT * FROM " . table('weixin_wxfans') . " WHERE openid = :openid", array(':openid' => $order['openid']));
    $alipay_alifans = mysqld_selectall("SELECT * FROM " . table('alipay_alifans') . " WHERE openid = :openid", array(':openid' => $order['openid']));
    include page('order');
}