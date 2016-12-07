<?php

$member=get_member_account(false);
$openid = $member['openid'];
$weixin_openid = $member['weixin_openid'];//2016-12-4-yanru-原来没有现在加上，后面代码都需要优先判断是否微信登录
$id = $profile['id'];
$op = $_GP['op'];
$settings=globaSetting();
$rebacktime=intval($settings['shop_postsale']);
$today = time();
$order_iscomment = 1;
if ($op == 'cancelsend')
{
    $orderid = intval($_GP['orderid']);
//    if(!empty($weixin_openid)){
//        $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND weixin_openid = :weixin_openid", array(':id' => $orderid, ':weixin_openid' => $weixin_openid ));
//    }else
    if(!empty($openid)){
        $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
    }
    if (empty($item)||$item['status']<0)
    {
        message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
    }
    //if(($item['paytype']==3&&$item['status']==1)||$item['status']==0)
    //{
    //    mysqld_update('shop_order', array('status' => -1,'updatetime'=>time()), array('id' => $orderid, 'openid' => $openid ));
    //    message('订单已关闭！', mobile_url('myorder'), 'success');
    //}
    if($item['status']==3)
    {
        message('请确认是否受到货物！');
    }
//    if(!empty($weixin_openid)){
//        mysqld_update('shop_order', array('status' => -1,'updatetime'=>time()), array('id' => $orderid, 'weixin_openid' => $weixin_openid));
//    }else
    if(!empty($openid)){
        mysqld_update('shop_order', array('status' => -1,'updatetime'=>time()), array('id' => $orderid, 'openid' => $openid));
    }
    $_GP['status'] = 99;
    //message('该订单不可取消');
}
if ($op == 'returngood')
{
    $orderid = intval($_GP['orderid']);
//    if(!empty($weixin_openid)){
//        $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND weixin_openid = :weixin_openid", array(':id' => $orderid, ':weixin_openid' => $weixin_openid ));
//    }else
    if(!empty($openid)){
        $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
    }

    if(!empty($item['updatetime']))
    {
        if(($item['updatetime'])<(time()-($rebacktime * 24 * 60 * 60)))
        {
            message("退货申请时间已过无法退货。");
        }
    }
    else
    {
        if(($item['createtime'])<(time()-($rebacktime * 24 * 60 * 60)))
        {
            message("退货申请时间已过无法退货。");
        }
    }
    $dispatch = mysqld_select("select id,dispatchname,sendtype from " . table('shop_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));
    if (empty($item))
    {
        message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
    }
    $opname="退货";
    if (checksubmit("submit"))
    {
//        if(!empty($weixin_openid)){
//            mysqld_update('shop_order', array('status' => -4,'isrest'=>1,'rsreson' => $_GP['rsreson']), array('id' => $orderid, 'weixin_openid' => $weixin_openid ));
//        }else
        if(!empty($openid)){
            mysqld_update('shop_order', array('status' => -4,'isrest'=>1,'rsreson' => $_GP['rsreson']), array('id' => $orderid, 'openid' => $openid ));
        }
        message('申请退货成功，请等待审核！', mobile_url('myorder',array('status' => intval($_GP['fromstatus']))), 'success');
    }
    include themePage('order_detail_return');
    exit;
}
if ($op == 'resendgood')
{
    $orderid = intval($_GP['orderid']);
//    if(!empty($weixin_openid)){
//        $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND weixin_openid = :weixin_openid", array(':id' => $orderid, ':weixin_openid' => $weixin_openid ));
//    }else
    if(!empty($openid)){
        $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
    }
    $dispatch = mysqld_select("select id,dispatchname,sendtype from " . table('shop_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));

    if(!empty($item['updatetime']))
    {
        if(($item['updatetime'])<(time()-($rebacktime * 24 * 60 * 60)))
        {
            message("换货申请时间已过无法换货。");
        }
    }
    else
    {
        if(($item['createtime'])<(time()-($rebacktime * 24 * 60 * 60)))
        {
            message("换货申请时间已过无法换货。");
        }
    }
    if (empty($item))
    {
        message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
    }
    $opname="换货";
    if (checksubmit("submit"))
    {
//        if(!empty($weixin_openid)){
//            mysqld_update('shop_order', array('status' =>  -3,'isrest'=>1,'rsreson' => $_GP['rsreson']), array('id' => $orderid, 'weixin_openid' => $weixin_openid ));
//        }else
        if(!empty($openid)){
            mysqld_update('shop_order', array('status' =>  -3,'isrest'=>1,'rsreson' => $_GP['rsreson']), array('id' => $orderid, 'openid' => $openid ));
        }
        message('申请换货成功，请等待审核！', mobile_url('myorder',array('status' => intval($_GP['fromstatus']))), 'success');
    }
    include themePage('order_detail_return');
    exit;
}

if ($op == 'returncomment')
{
//    $orderid = intval($_GP['orderid']);
//    //2016-11-17-yanru-begin-原来是对同一订单不同商品进行评价，现在是同一评价
//    //$ogsid = intval($_GP['ogsid']);
//    $list = mysqld_selectall("SELECT comment.*,member.realname,member.mobile FROM " . table('shop_goods_comment') . " comment  left join " . table('member') . " member on comment.openid=member.openid WHERE comment.orderid=:orderid and comment.openid=:openid ", array(':orderid' => $orderid, 'openid' => $openid ));
//    $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
////    $shop_order = mysqld_select("SELECT * FROM " . table('shop_order_goods') . " WHERE id = :id", array(':id' => $ogsid ));
////    if(empty($shop_order['id']))
////    {
////        message('商品不能空', refresh(), 'error');
////    }
//    if (checksubmit("submit"))
//    {
//        $optionid = intval($_GP['optionid']);
//        $option = mysqld_select("select * from " . table("shop_goods_option") . " where id=:id limit 1", array(":id" => $optionid));
//
////        if($item['status']!=3)
////        {
////            message('订单未完成不能评论', refresh(), 'error');
////        }
//        if(empty($_GP['rsreson']))
//        {
//            message('请输入评论内容', refresh(), 'error');
//        }
//        $shop_order_goods = mysqld_select("select * from " . table("shop_order_goods") . " where id=:id limit 1", array(":id" => $ogsid));
//        if(!empty($shop_order_goods['iscomment']))
//        {
//            message('订单已评论', refresh(), 'error');
//        }
//        mysqld_insert('shop_goods_comment', array('createtime'=>time(),'rate'=> $_GP['rate'],'ordersn' => $item['ordersn'],'optionname'=>$option['title'],'goodsid'=> $shop_order['goodsid'],'comment' => $_GP['rsreson'],'orderid' => $orderid, 'openid' => $openid ));
//        mysqld_update('shop_order_goods', array('iscomment'=>1 ),array('id'=>$ogsid));
//
//        message('评论成功！', mobile_url('myorder',array('status' => intval($_GP['fromstatus']))), 'success');
//    }
//    include themePage('order_detail_comment');
//    exit;

    //2016-12-4-yanru-begin
    if(!empty($_GP['comment'])){
        $orderid = intval($_GP['orderid']);
        $ordersn = $_GP['ordersn'];
        $order_temp = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND ordersn = :ordersn", array(':id' => $orderid, ':ordersn' => $ordersn ));
        $order_shop_goods = mysqld_selectall("SELECT * FROM " . table('shop_order_goods') . " WHERE orderid = :orderid ", array(':orderid' => $orderid));
        foreach ($order_shop_goods as $goods){
            mysqld_update('shop_order_goods', array('iscomment' => 1), array('id' => $goods['id'], 'orderid' => $goods['orderid'] ));
            $comment_date = array('createtime'=>time(),
                'optionname'=>$goods['optionname'],
                'orderid'=>$goods['orderid'],
                'ordersn'=>$order_temp['ordersn'],
                'openid'=>$order_temp['openid'],
                'weixin_openid'=>$order_temp['weixin_openid'],
                'comment'=>$_GP['comment'],
                'rate' => -1,
                'goodsid' => $goods['goodsid']);
            mysqld_insert('shop_goods_comment', $comment_date);
        }
        if($order_temp['status'] == 4){
            mysqld_update('shop_order', array('status' => 5), array('id' => $orderid, 'ordersn' => $ordersn));
        }
    }
    //end
}
if ($op == 'returnpay')
{
    $orderid = intval($_GP['orderid']);
//    if(!empty($weixin_openid)){
//        $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND weixin_openid = :weixin_openid", array(':id' => $orderid, ':weixin_openid' => $weixin_openid ));
//    }else
    if(!empty($openid)){
        $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
    }
    $dispatch = mysqld_select("select id,dispatchname,sendtype from " . table('shop_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));
           
    if (empty($item))
    {
        message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
    }
    $opname="退款";
    if (checksubmit("submit"))
    {
        if($order['paytype']==3)
        {
            message('货到付款订单不能进行退款操作!', refresh(), 'error');
        }
//        if(!empty($weixin_openid)){
//            mysqld_update('shop_order', array('status' => -2,'rsreson' => $_GP['rsreson']), array('id' => $orderid, 'weixin_openid' => $weixin_openid ));
//        }else
        if(!empty($openid)){
            mysqld_update('shop_order', array('status' => -2,'rsreson' => $_GP['rsreson']), array('id' => $orderid, 'openid' => $openid ));
        }
        require(WEB_ROOT.'/system/common/extends/class/shopwap/class/mobile/myorder_2.php');
        message('申请退款成功，请等待审核！', mobile_url('myorder',array('status' => intval($_GP['fromstatus']))), 'success');
    }
    include themePage('order_detail_return');
    exit;
}
elseif ($op == 'confirm')
{
    $orderid = intval($_GP['orderid']);
//    if(!empty($weixin_openid)){
//        $order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND weixin_openid = :weixin_openid", array(':id' => $orderid, ':openid' => $weixin_openid ));
//    }else
    if(!empty($openid)){
        $order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
    }
    if (empty($order))
    {
        message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
    }
    //2016-11-17-yanru-下面是用户增加积分功能暂时屏蔽
//    if (empty($order['isrest']))
//    {//不是换货
//        $this->setOrderCredit($openid,$order['id'],true,'订单:'.$order['ordersn'].'收货新增积分');
//    }
    //end
//    if(!empty($weixin_openid)){
//        mysqld_update('shop_order', array('status' => 4,'updatetime'=>time()), array('id' => $orderid, 'weixin_openid' => $weixin_openid ));
//    }else
    if(!empty($openid)){
        mysqld_update('shop_order', array('status' => 4,'updatetime'=>time()), array('id' => $orderid, 'openid' => $openid ));
    }
//    $settings=globaSetting();
//
//    require(WEB_ROOT.'/system/common/extends/class/shopwap/class/mobile/myorder_1.php');
//    message('确认收货完成！', mobile_url('myorder',array('status' => intval($_GP['fromstatus']))), 'success');
    $url = WEBSITE_ROOT.mobile_url('myorder',array('status' => 99));
    header("location:".WEBSITE_ROOT.mobile_url('myorder',array('status' => 99)));
}
else if ($op == 'detail')
{
    $orderid = intval($_GP['orderid']);
//    if(!empty($weixin_openid)){
//        $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE weixin_openid = '".$weixin_openid."' and id='{$orderid}' limit 1");
//    }else
    if(!empty($openid)){
        $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE openid = '".$openid."' and id='{$orderid}' limit 1");
    }
    if (empty($item))
    {
        message('抱歉，您的订单不存或是已经被取消！', mobile_url('myorder'), 'error');
    }
    if($item['hasbonus'])
    {
        $bonuslist = mysqld_selectall("SELECT bonus_user.*,bonus_type.type_name FROM " . table('bonus_user') . " bonus_user left join  " . table('bonus_type') . " bonus_type on bonus_type.type_id=bonus_user.bonus_type_id WHERE bonus_user.order_id=:order_id",array(":order_id"=>$orderid));
    }
//    if(!empty($weixin_openid)){
//        if($item['paytype']!=$this->getPaytypebycode($item['paytypecode']))
//        {
//            mysqld_update('shop_order', array('paytype' => $this->getPaytypebycode($item['paytypecode'])), array('id' => $orderid, 'weixin_openid' => $weixin_openid ));
//            $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE weixin_openid = '".$weixin_openid."' and id='{$orderid}' limit 1");
//        }
//        //2016-11-17-yanru-begin-change status 0 to 1 begin
//        if(0==$item['status'] && ($today - 30*60 > $item['createtime']))
//        {
//            mysqld_update('shop_order', array('status' => 1), array('id' => $orderid, 'weixin_openid' => $weixin_openid ));
//            $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE weixin_openid = '".$weixin_openid."' and id='{$orderid}' limit 1");
//        }
//        //end
//    }else
    if(!empty($openid)){
        if($item['paytype']!=$this->getPaytypebycode($item['paytypecode']))
        {
            mysqld_update('shop_order', array('paytype' => $this->getPaytypebycode($item['paytypecode'])), array('id' => $orderid, 'openid' => $openid ));
            $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE openid = '".$openid."' and id='{$orderid}' limit 1");
        }
        //2016-11-17-yanru-begin-change status 0 to 1 begin
        if(0==$item['status'] && ($today - 30*60 > $item['createtime']))
        {
            mysqld_update('shop_order', array('status' => 1), array('id' => $orderid, 'openid' => $openid ));
            $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE openid = '".$openid."' and id='{$orderid}' limit 1");
        }
        //end
    }
    $goodsid = mysqld_selectall("SELECT goodsid,total FROM " . table('shop_order_goods') . " WHERE orderid = '{$orderid}'", array(), 'goodsid');
    $goods = mysqld_selectall("SELECT g.id, g.title, g.thumb, g.marketprice,o.total,o.optionid,o.iscomment,o.id as ogsid FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.goodsid=g.id "
        . " WHERE o.orderid='{$orderid}'");
    foreach ($goods as &$g)
    {
        //属性
        $option = mysqld_select("select * from " . table("shop_goods_option") . " where id=:id limit 1", array(":id" => $g['optionid']));
        if ($option) {
            //$g['title'] = "[" . $option['title'] . "]" . $g['title'];
            //$g['marketprice'] = $option['marketprice'];
            $g['option'] = $option;
        }
        //2016-12-4-yanru-begin
        if($g['iscomment'] == 0)
            $order_iscomment = 0;
        //end
    }
    $item['iscomment'] = $order_iscomment;//2016-12-4-yanru
    unset($g);

    $dispatch = mysqld_select("select id,dispatchname,sendtype from " . table('shop_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));
    $payments = mysqld_selectall("select * from " . table("payment")." where enabled=1 order by `order` desc");
  
    if($item['status'] >= 3){
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

        $item_order_express = array();
        for($i = 0; $i < count($item_express); $i++){
            if(!empty($item_order_express)){
                foreach ($item_order_express as $temp_order_express){
                    if(
                        0 != strcmp($item_express[$i][0], $temp_order_express['goodssn']) &&
                        (
                            (0 != strcmp($item_express[$i][1], $temp_order_express['express'])) ||
                            (0 != strcmp($item_expresssn[$i][1], $temp_order_express['expresssn']) && 0 == strcmp($item_express[$i][1], $temp_order_express['express']))
                        )
                    ){
                        $item_order_express[] = array("goodssn"=>$item_express[$i][0], "expresscom"=>$item_expresscom[$i][1], "expresssn"=>$item_expresssn[$i][1], "express"=>$item_express[$i][1]);
                    }
                }
            }else {
                $item_order_express[] = array("goodssn" => $item_express[$i][0], "expresscom" => $item_expresscom[$i][1], "expresssn" => $item_expresssn[$i][1], "express" => $item_express[$i][1]);
            }
        }
        //end
    }

    include themePage('order_detail');
    exit;
}
else
{
    $pindex = max(1, intval($_GP['page']));
    $psize = 20;

    $status = intval($_GP['status']);
//    if(!empty($weixin_openid)){
//        $where = "weixin_openid = '".$weixin_openid."'";
//    }else
    if(!empty($openid)){
        $where = "openid = '".$openid."'";
    }
    ;
    if ($status == -5)
    {
        $where.=" and ( status=-2 or status=-3 or status=-4 )";
    }
    else if ($status == 99)
    {
        // $where.=" and ( status=-5 or status=-6 or status=3 )";
        //2016-11-17-yanru-begin
        $where.=" and ( status >= 0)";
    }
    else if($status == 3)
    {
        $where.=" and ( status=3 or status=4 or status=5 )";
    }
    else
    {
        $where.=" and status=$status";
    }
    //$list = mysqld_selectall("SELECT * FROM " . table('shop_order') . " WHERE $where ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(), 'id');
    $list = mysqld_selectall("SELECT * FROM " . table('shop_order') . " WHERE $where ORDER BY status ASC, createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(), 'id');
    $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_order') . " WHERE  $where ");
    $pager = pagination($total, $pindex, $psize);

    if (!empty($list))
    {
        foreach ($list as &$row)
        {
            //2016-12-4-yanru-begin
//            $goods = mysqld_selectall("SELECT g.id, g.title, g.thumb, g.marketprice,o.total,o.optionid FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.goodsid=g.id "
//                . " WHERE o.orderid='{$row['id']}'");
            $goods = mysqld_selectall("SELECT g.id, g.title, g.thumb, g.marketprice,o.total,o.optionid,o.iscomment FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.goodsid=g.id "
                . " WHERE o.orderid='{$row['id']}'");
            $order_iscomment = 1;
            //end
            foreach ($goods as &$item)
            {
                //属性
                $option = mysqld_select("select id,title,marketprice,weight,stock from " . table("shop_goods_option") . " where id=:id limit 1", array(":id" => $item['optionid']));
                if ($option)
                {
                    //2016-11-03-yanru-begin
                    //$item['title'] = "[" . $option['title'] . "]" . $item['title'];
                    //$item['marketprice'] = $option['marketprice'];
                    $item['attribute_id'] = $option['id'];
                    $item['attribute_title'] = $option['title'];
                    $item['attribute_price'] = $option['marketprice'];
                    $item['stock'] = $option['stock'];
                    //end
                }

                //2016-12-4-yanru-begin
                if($item['iscomment'] == 0)
                    $order_iscomment = 0;
                //end
            }
            unset($item);
            $row['goods'] = $goods;
            $row['total'] = $goodsid;
            $row['dispatch'] = mysqld_select("select id,dispatchname from " . table('shop_dispatch') . " where id=:id limit 1", array(":id" => $row['dispatch']));
            $row['iscomment'] = $order_iscomment;
        }
    }
    include themePage('order');
    exit;
}