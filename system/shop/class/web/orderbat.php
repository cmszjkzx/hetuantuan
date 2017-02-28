<?php

$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
if ($operation == 'display') {

    if (checksubmit('sendbatexpress')) {
        $index=0;
        if(!empty($_GP['check']))
        {
            if(!empty($_CMS[WEB_SESSION_ACCOUNT]['is_admin'])) {
                foreach ($_GP['check'] as $k) {
                    $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id", array(':id' => $k));

                    $isexpress = $_GP['express' . $k];
                    if ($isexpress != '-1' && empty($_GP['expressno' . $k])) {
                        message('订单' . $item['ordersn'] . '没有快递单号，请填写完整！');
                    }
                    if ($item['status'] > 2 || $item['status'] < 0) {
                        message('订单' . $item['ordersn'] . '状态不是待发货状态，请重新点击”批量发货“后进行操作。');
                    }
                    $express = $_GP['express' . $k];
                    if ($express == '-1') {
                        $express == '';
                    }
                    if ($item['paytypecode'] == 'bank' || $item['paytypecode'] == 'delivery') {
                        updateOrderStock($k);
                    }
                    mysqld_update('shop_order', array(
                        'status' => 3,
                        'express' => $express,
                        'expresscom' => $_GP['expresscom' . $k],
                        'expresssn' => $_GP['expressno' . $k],
                    ), array('id' => $k));
                    $index = $index + 1;
                }
            }else{
                foreach ($_GP['check'] as $k) {
                    $item = mysqld_select("SELECT o.*, og.id AS ordergoodsid, og.goodsid AS goodsid, og.optionid AS optionid, g.title AS goodtitle, og.optionname AS optionname FROM "
                        . table('shop_order') . " o LEFT JOIN ".table('shop_order_goods') ." og ON o.id=og.orderid LEFT JOIN ".table('shop_goods')
                        ." g ON g.id=og.goodsid WHERE og.id = :id", array(':id' => $k));
                    $isexpress = $_GP['express' . $k];
                    if(empty($item)){
                        message('数据库查询失败！');
                    }
                    if ($isexpress != '-1' && empty($_GP['expressno' . $k])) {
                        message('订单' . $item['ordersn'] . '没有快递单号，请填写完整！');
                    }
                    if ($item['status'] > 3 || $item['status'] < 0) {
                        message('订单' . $item['ordersn'] . '状态不是待发货状态，请重新点击”批量发货“后进行操作。');
                    }
                    $express = $_GP['express' . $k];
                    if ($express == '-1') {
                        $express == '';
                    }
                    if ($item['paytypecode'] == 'bank' || $item['paytypecode'] == 'delivery') {
                        updateOrderStock($k);
                    }

                    $temp_goods = $item['goodsid']."@".$item['optionid'];
                    $temp_goods_expresscom = $temp_goods."_".$_GP['expresscom'.$k].";";
                    $temp_goods_expresssn = $temp_goods."_".$_GP['expressno'.$k].";";
                    $temp_goods_express = $temp_goods."_".$express.";";
                    //需要比较下是用strpos还是用stristr
                    if(stristr($item['expresssn'], $temp_goods)==false && stristr($item['express'], $temp_goods)==false){
                        $temp_goods_expresscom = $item['expresscom'].$temp_goods_expresscom;
                        $temp_goods_expresssn = $item['expresssn'].$temp_goods_expresssn;
                        $temp_goods_express = $item['express'].$temp_goods_express;
                        mysqld_update('shop_order_goods', array(
                            'status' => 12,
                            'express' => $express,
                            'expresscom' => $_GP['expresscom'.$k],
                            'expresssn' => $_GP['expressno'.$k],),
                            array('id' => $k, 'orderid' => $item['id'], 'goodsid'=>$item['goodsid']));

                        $notice = array(
                            "touser" => $item['weixin_openid'],
                            "template_id" => "A-pOebjfRNtuzGSqEVnGwgtjk1Hqt3G9GOpavMVHzb0",
                        );
                        $first = array(
                            "value" => "您的订单物流信息已更新！",
                            "color" => "#173177"
                        );
                        $keyword1 = array(
                            "value" => $item['goodtitle'].":".$item['optionname'],
                            "color" => "#173177"
                        );
                        $keyword2 = array(
                            "value" => $item['ordersn'],
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
                        mysqld_update("shop_order", $order_update, array("id" => $item['id']));
                    }
                    $index = $index + 1;
                }
            }
            //end
            message('批量发货操作完成,成功处理'.$index.'条订单', refresh(), 'success');
        }else{
            message('请勾选需要批量导入的订单!');
        }
    }

    if(!empty($_CMS[WEB_SESSION_ACCOUNT]['is_admin'])){
        $status = 2;
        $condition .= " AND status = '" . intval($status) . "'";
        $list = mysqld_selectall("SELECT * FROM " . table('shop_order') . " WHERE 1=1 $condition ");
        $total = mysqld_selectcolumn("SELECT count(*) FROM " . table('shop_order') . " WHERE 1=1 $condition ");
    }else{
        $list = mysqld_selectall("SELECT o.*, og.id AS ordergoodsid FROM " . table('shop_order_goods') . " og LEFT JOIN ".table('shop_goods') ." g ON og.goodsid=g.id LEFT JOIN ".table('shop_order')
            ." o ON og.orderid=o.id WHERE (o.status=2 OR (o.status=3 AND (og.status=11 OR og.status=0))) AND g.band=:band AND o.ordersn IS NOT NULL AND o.expresssn NOT LIKE concat('%',og.goodsid,'@',og.optionid,'%') "
            , array(':band'=>$_CMS[WEB_SESSION_ACCOUNT]['groupName']));
        $total = mysqld_selectcolumn("SELECT count(o.*) AS total FROM " . table('shop_order_goods') . " og LEFT JOIN ".table('shop_goods') ." g ON og.goodsid=g.id LEFT JOIN "
            .table('shop_order') ." o ON og.orderid=o.id WHERE (o.status=2 OR (o.status=3 AND (og.status=11 OR og.status=0))) AND g.band=:band AND o.ordersn IS NOT NULL AND o.expresssn NOT LIKE concat('%',og.goodsid,'@',og.optionid,'%') "
            , array(':band'=>$_CMS[WEB_SESSION_ACCOUNT]['groupName']));
    }

    $dispatchs = mysqld_selectall("SELECT * FROM " . table('shop_dispatch') );
    $dispatchdata=array();
    if(is_array($dispatchs)) {
        foreach($dispatchs as $disitem) {
            $dispatchdata[$disitem['id']]=$disitem;
        }
    }

    $dispatchlist = mysqld_selectall("SELECT * FROM " . table('dispatch')." where sendtype=0" );

    include page('orderbat');
}