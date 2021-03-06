<?php

$member=get_member_account(false);
$openid = $member['openid'];
$weixin_openid = $member['weixin_openid'];//2016-12-4-yanru-原来没有现在加上，后面代码都需要优先判断是否微信登录
$id = $profile['id'];
$op = $_GP['op'];
$settings=globaSetting();
$rebacktime=intval($settings['shop_postsale']);
$today = time();
$return_message;
if ($op == 'cancelsend')
{
    $orderid = intval($_GP['orderid']);

    if(!empty($openid)){
        $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
    }
    if (empty($item)||$item['status']<0)
    {
        //message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
        $return_message = '抱歉，您的订单不存在!';
    }
    //if(($item['paytype']==3&&$item['status']==1)||$item['status']==0)
    //{
    //    mysqld_update('shop_order', array('status' => -1,'updatetime'=>time()), array('id' => $orderid, 'openid' => $openid ));
    //    message('订单已关闭！', mobile_url('myorder'), 'success');
    //}
    if($item['status']==3)
    {
        //message('请确认是否受到货物！');
        $return_message = '请确认是否受到货物!';
    }

    //2016-12-14-yanru-begin
    if($item['status']==0 || $item['status']==1)
    {
        if($item['hasbonus']) {
            //2017-04-07-yanru-优惠券更新，需要判断优惠券是否是大礼包
            $packagebonus = mysqld_select("select * from ".table("package_bonus_user")." where orderid=:orderid and  openid=:openid ", array(':orderid'=>$item['id'], ':openid'=>$openid));
            if(empty($packagebonus)) {
                $data = array('isuse' => 0,
                    'used_time' => 0,
                    'order_id' => 0);
                mysqld_update('bonus_user', $data, array('order_id' => $item['id'], 'openid' => $openid));
            }else{
                $data = array('isuse' => 0,
                    'used_time' => 0,
                    'orderid' => 0);
                mysqld_update('package_bonus_user', $data, array('orderid' => $item['id'], 'openid' => $openid));
            }
        }
        //2017-04-20-yanru-更新用户订单状态为了区别已付款订单，这时更新状态为-1
        mysqld_update('shop_order', array('status' => -1,'updatetime'=>time()), array('id' => $orderid, 'openid' => $openid));

    }
    //end

    if($item['status'] >= 3){
        mysqld_update('shop_order', array('status' => -7,'updatetime'=>time()), array('id' => $orderid, 'openid' => $openid));
    }
    //message('该订单不可取消');
    //2016-12-16-yanru
    if(empty($_GP['returnid']))
        header("location:".create_url('mobile',array('do'=>$_GP['do'],'name'=>$_GP['name'],'op'=>'','status'=>$item['status'])));
    else
        header("location:".create_url('mobile',array('do'=>$_GP['do'],'name'=>$_GP['name'],'op'=>'','status'=>$_GP['returnid'])));
    exit;
    //die(json_encode(array('message'=>$return_message,'do'=>$_GP['do'],'name'=>$_GP['name'],'op'=>'','status'=>$item['status'])));
}
if ($op == 'returngood')
{
    $orderid = intval($_GP['orderid']);

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
    $result = 0;
    if(!empty($_GP['comment'])){
        $orderid = intval($_GP['orderid']);
        $ordersn = $_GP['ordersn'];
        //$order_temp = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND ordersn = :ordersn", array(':id' => $orderid, ':ordersn' => $ordersn ));
        $order_temp = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id ", array(':id' => $orderid));
        $order_status = $order_temp['status'];
        $order_shop_goods = mysqld_selectall("SELECT * FROM " . table('shop_order_goods') . " WHERE orderid = :orderid ", array(':orderid' => $orderid));
        foreach ($order_shop_goods as $goods){
            mysqld_update('shop_order_goods', array('iscomment' => 1), array('id' => $goods['id'], 'orderid' => $goods['orderid'] ));
            $comment_date = array('createtime'=>time(),
                'optionname'=>$goods['optionname'],
                'orderid'=>$goods['orderid'],
                //'ordersn'=>$order_temp['ordersn'],
                'ordersn'=>$ordersn,
                'openid'=>$order_temp['openid'],
                'weixin_openid'=>$order_temp['weixin_openid'],
                'comment'=>$_GP['comment'],
                'rate' => -1,
                'goodsid' => $goods['goodsid']);
            mysqld_insert('shop_goods_comment', $comment_date);
        }
        if($order_status == 4){
            //mysqld_update('shop_order', array('status' => 5), array('id' => $orderid, 'ordersn' => $ordersn));
            mysqld_update('shop_order', array('status' => 5), array('id' => $orderid));
            $order_status = 5;
        }
        $result = 1;
    }
    die(json_encode(array('result' => $result,
        'do'=>$_GP['do'],
        'name'=>$_GP['name'],
        'status'=>$order_status)));
    exit;
}
if ($op == 'returnpay')
{
    $orderid = intval($_GP['orderid']);

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

    if(!empty($openid)){
        $order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
    }
    if (empty($order))
    {
        //message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
        $return_message = '抱歉，您的订单不存在或是已经被取消!';
    }
    //2016-11-17-yanru-下面是用户增加积分功能暂时屏蔽
//    if (empty($order['isrest']))
//    {//不是换货
//        $this->setOrderCredit($openid,$order['id'],true,'订单:'.$order['ordersn'].'收货新增积分');
//    }
    //end
//    $settings=globaSetting();
//
//    require(WEB_ROOT.'/system/common/extends/class/shopwap/class/mobile/myorder_1.php');
//    message('确认收货完成！', mobile_url('myorder',array('status' => intval($_GP['fromstatus']))), 'success');
    $comment_goods = mysqld_select("SELECT * FROM " . table('shop_goods_comment') . " WHERE ordersn = :ordersn AND openid = :openid", array(':ordersn' => $order['ordersn'], ':openid' => $openid ));
    if(!empty($order)){
        if(empty($comment_goods))
            mysqld_update('shop_order', array('status' => 4,'updatetime'=>time()), array('id' => $orderid, 'openid' => $openid ));
        else
            mysqld_update('shop_order', array('status' => 5,'updatetime'=>time()), array('id' => $orderid, 'openid' => $openid ));
        //2017-1-5-yanru-begin-更新该订单下每个商品到货状态
        mysqld_update('shop_order_goods', array('status' => 13), array('orderid' => $orderid));
        //end

    }
    //2016-12-16-yanru
    //header("location:".create_url('mobile',array('do'=>$_GP['do'],'name'=>$_GP['name'],'op'=>'','status'=>$order['status'])));
    if(empty($_GP['returnid']))
        header("location:".create_url('mobile',array('do'=>$_GP['do'],'name'=>$_GP['name'],'op'=>'','status'=>$item['status'])));
    else
        header("location:".create_url('mobile',array('do'=>$_GP['do'],'name'=>$_GP['name'],'op'=>'','status'=>$_GP['returnid'])));
    exit;

//    die(json_encode(array('reslut'=>$return_message,'do'=>$_GP['do'],'name'=>$_GP['name'],'op'=>'','status'=>$status)));
}
else if ($op == 'detail')
{
    $order_iscomment = 0;
    $orderid = intval($_GP['orderid']);
    if(empty($openid)){
       message('抱歉，您还没有登录！', mobile_url('confirm'));
    }
    $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE openid = '".$openid."' and id='{$orderid}' limit 1");
    if (empty($item))
    {
        message('抱歉，您的订单不存或是已经被取消！', mobile_url('myorder'), 'error');
    }
    if($item['hasbonus'])
    {
        if($item['status']==1){
            $bonusprice=0;
            $bonuslist = mysqld_selectall("SELECT bonus_user.*,bonus_type.type_name, bonus_type.type_money FROM " . table('bonus_user') . " bonus_user left join  " . table('bonus_type') . " bonus_type on bonus_type.type_id=bonus_user.bonus_type_id WHERE bonus_user.order_id=:order_id",array(":order_id"=>$orderid));
            $data = array('isuse'=>0,
                'used_time'=>0,
                'order_id'=>0);
            if(!empty($bonuslist)){
                foreach ($bonuslist as $bonus){
                    $bonusprice += $bonus['type_money'];
                    mysqld_update('bonus_user', $data, array('bonus_id'=>$bonus['bonus_id']));
                }
            }else{
                //2017-04-07-yanru-优惠券更新，需要判断优惠券是否是大礼包
                $data = array('isuse'=>0,
                    'used_time'=>0,
                    'orderid'=>0);
                mysqld_update('package_bonus_user', $data, array('orderid' => $orderid, 'openid' => $openid));
            }
        }else{
            $bonusprice=0;
            //$bonuslist = mysqld_selectall("SELECT bonus_user.*,bonus_type.type_name FROM " . table('bonus_user') . " bonus_user left join  " . table('bonus_type') . " bonus_type on bonus_type.type_id=bonus_user.bonus_type_id WHERE bonus_user.order_id=:order_id",array(":order_id"=>$orderid));
            //2016-12-8-yanru-获取订单优惠券信息
            $bonuslist = mysqld_selectall("SELECT bonus_user.*,bonus_type.type_name, bonus_type.type_money FROM " . table('bonus_user') . " bonus_user left join  " . table('bonus_type') . " bonus_type on bonus_type.type_id=bonus_user.bonus_type_id WHERE bonus_user.order_id=:order_id",array(":order_id"=>$orderid));
            if(!empty($bonuslist)){
                foreach ($bonuslist as $bonus){
                    $bonusprice += $bonus['type_money'];
                }
            }
        }
    }

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
    $goods = mysqld_selectall("SELECT g.id, g.title, g.thumb, g.marketprice,o.total,o.optionid,o.iscomment,o.id as ogsid,o.goodsid as goodsid,o.optionname as optionname FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.goodsid=g.id "
        . " WHERE o.orderid='{$orderid}'");
    foreach ($goods as &$g)
    {
        //属性
        $option = mysqld_select("select * from " . table("shop_goods_option") . " where id=:id limit 1", array(":id" => $g['optionid']));
        if ($option) {
            //$g['title'] = $g['title'];
            $g['marketprice'] = $option['marketprice'];
            $g['option'] = $option;
        }
        //2016-12-4-yanru-begin
        if($g['iscomment'] == 1)
            $order_iscomment = 1;
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
        //2016-12-26-yanru-begin-根据要求修改订单后台数据
        $packages = array();
        $temp_package = "";
        if(!empty($item_express)){
            for($i = 0; $i < count($item_express); $i++){
                if(empty($temp_package)){
                    $packages[] = array('number'=>intval(strlen($temp_package)/10)+1, 'expresscom'=>$item_expresscom[$i][1],
                        'expresssn'=>$item_expresssn[$i][1], 'express'=>$item_express[$i][1]);
                    $temp_package .= $item_expresssn[$i][1].";";
                }
                if(stristr($temp_package, $item_expresssn[$i][1]) == false){
                    $packages[] = array('number'=>intval(strlen($temp_package)/10)+1, 'expresscom'=>$item_expresscom[$i][1],
                        'expresssn'=>$item_expresssn[$i][1], 'express'=>$item_express[$i][1]);
                    $temp_package .= $item_expresssn[$i][1].";";
                }
                for($j = 0; $j < count($goods); $j++){
                    if(strpos($item_express[$i][0], "@")){
                        $temp_goodoption = explode("@", $item_express[$i][0]);
                        if($goods[$j]['goodsid']==$temp_goodoption[0] && $goods[$j]['optionid']==$temp_goodoption[1]){
                            $goods[$j]['expresscom'] = $item_expresscom[$i][1];
                            $goods[$j]['expresssn'] = $item_expresssn[$i][1];
                            $goods[$j]['express'] = $item_express[$i][1];
                            $goods[$j]['packagenumber'] = intval(strpos($temp_package, $item_expresssn[$i][1])/10)+1;
                        }
                    }else{
                        if($goods[$j]['optionname']==$item_express[$i][0]||"inserttemp"==$item_express[$i][0]){
                            $goods[$j]['expresscom'] = $item_expresscom[$i][1];
                            $goods[$j]['expresssn'] = $item_expresssn[$i][1];
                            $goods[$j]['express'] = $item_express[$i][1];
                            $goods[$j]['packagenumber'] = intval(strpos($temp_package, $item_expresssn[$i][1])/10)+1;
                        }
                    }
                }
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
        $where.=" and ( status=3)";
    }
    else
    {
        $where.=" and status=$status";
    }
    //$list = mysqld_selectall("SELECT * FROM " . table('shop_order') . " WHERE $where ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(), 'id');
    //$list = mysqld_selectall("SELECT * FROM " . table('shop_order') . " WHERE $where ORDER BY status ASC, createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(), 'id');
    $list = mysqld_selectall("SELECT * FROM " . table('shop_order') . " WHERE $where ORDER BY createtime DESC, status ASC ", array(), 'id');
    $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_order') . " WHERE  $where ");
    $pager = pagination($total, $pindex, $psize);

    if (!empty($list))
    {
        foreach ($list as &$row)
        {
            $order_iscomment = 0;
            //2016-12-4-yanru-begin-获取订单商品的评价字段，判断是否已经评价
            $goods = mysqld_selectall("SELECT g.id, g.title, g.thumb, g.marketprice, g.total as stock, o.total, o.optionid, o.iscomment, o.optionname FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.goodsid=g.id "
                . " WHERE o.orderid='{$row['id']}'");
            //2016-12-7-yanru-begin-可能会存在BUG，但是暂时没有出现
            if($row['status'] == 0){
                if (30*60 < time()-$row['createtime']){
                    $row['status'] = 1;
                    mysqld_update("shop_order", array("status" => 1), array("id" => $row['id']));

                    //2017-04-17-yanru-新增当订单超时后自动更新优惠券
                    if($row['hasbonus']) {
                        //2017-04-17-yanru-优惠券更新，需要判断优惠券是否是大礼包
                        $packagebonus = mysqld_select("select * from ".table("package_bonus_user")." where orderid=:orderid and  openid=:openid ", array(':orderid'=>$row['id'], ':openid'=>$openid));
                        if(empty($packagebonus)) {
                            $data = array('isuse' => 0,
                                'used_time' => 0,
                                'order_id' => 0);
                            mysqld_update('bonus_user', $data, array('order_id' => $row['id'], 'openid' => $openid));
                        }else{
                            $data = array('isuse' => 0,
                                'used_time' => 0,
                                'orderid' => 0);
                            mysqld_update('package_bonus_user', $data, array('orderid' => $row['id'], 'openid' => $openid));
                        }
                    }
                }
            }
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
                    //$item['attribute_title'] = $option['title'];
                    $item['attribute_price'] = $option['marketprice'];
                    $item['stock'] = $option['stock'];
                    //end
                }else{
                    $item['attribute_price'] = $item['marketprice'];
                }

                //2016-12-4-yanru-begin
                if($item['iscomment'] == 1)
                    $order_iscomment = 1;
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