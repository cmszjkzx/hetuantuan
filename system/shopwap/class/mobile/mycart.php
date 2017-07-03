<?php

$member=get_member_account(false);
$openid = $member['openid'];
$op = $_GP['op'];
if ($op == 'add') {
    $goodsid = intval($_GP['id']);
    $total = intval($_GP['total']);
    $total = empty($total) ? 1 : $total;
    $optionid = intval($_GP['optionid']);
    $goods = mysqld_select("SELECT id, type, total,marketprice,isverify FROM " . table('shop_goods') . " WHERE id = :id and deleted=0 and status=1", array(':id' => $goodsid));
    if (empty($goods)) {
        $result['message'] = '抱歉，该商品不存在或是已经被删除！';
        message($result, '', 'ajax');
    }
    if (!empty($goods['isverify'])) {
        $result['message'] = '抱歉，核销商品不能加入购物车，请自己点击购买！';
        message($result, '', 'ajax');
    }
    $marketprice = $goods['marketprice'];
    $goodsOptionStock=0;
    $goodsStock=$goods['total'];
    $goodsOptionStock=$goods['total'];
    if (!empty($optionid)) {
        $option = mysqld_select("select marketprice,stock from " . table('shop_goods_option') . " where id=:id limit 1", array(":id" => $optionid));
        if (!empty($option)) {
            $marketprice = $option['marketprice'];
            $goodsOptionStock=$option['stock'];
        }
    }
    if($goodsOptionStock<$total&&$goodsOptionStock!=-1)
    {
        $result = array(
            'result' => 0,
            'maxbuy' => $goodsOptionStock
        );
        die(json_encode($result));
        exit;
    }
    $row = mysqld_select("SELECT id, total FROM " . table('shop_cart') . " WHERE session_id = :session_id  AND goodsid = :goodsid  and optionid=:optionid", array(':session_id' => $openid, ':goodsid' => $goodsid,':optionid'=>$optionid));
    if ($row == false) {
        //不存在
        $data = array(
            'goodsid' => $goodsid,
            'goodstype' => $goods['type'],
            'marketprice' => $marketprice,
            'session_id' => $openid,
            'total' => $total,
            'optionid' => $optionid
        );
        mysqld_insert('shop_cart', $data);
//        $goodsStock--;
//        $goodsOptionStock--;
//        $goods_new_stock = array('total' => $goodsStock);
//        $option_new_stock = array('stock' => $goodsOptionStock);
//        mysqld_update('shop_goods', $goods_new_stock, array('id' => $goodsid));
//        mysqld_update('shop_goods_option', $option_new_stock, array('id' => $optionid));
    } else {
        //累加最多限制购买数量
        $t = $total + $row['total'];
        //存在
        $data = array(
            'marketprice' => $marketprice,
            'total' => $t,
            'optionid' => $optionid
        );
        mysqld_update('shop_cart', $data, array('id' => $row['id']));
    }
    //返回数据
    $carttotal = $this->getCartTotal();
    $result = array(
        'result' => 1,
        'total' => $carttotal
    );
    die(json_encode($result));
} else if ($op == 'clear') {
    mysqld_delete('shop_cart', array('session_id' => $openid));
    die(json_encode(array("result" => 1)));
} else if ($op == 'remove') {
    $id = intval($_GP['id']);
    mysqld_delete('shop_cart', array('session_id' => $openid, 'id' => $id));
    die(json_encode(array("result" => 1, "cartid" => $id)));
} else if ($op == 'update') {
    $id = intval($_GP['id']);
    $num = intval($_GP['num']);
    $goods_option_stock =  mysqld_selectcolumn("SELECT sgo.stock FROM ".table('shop_cart')." sc LEFT JOIN ".table('shop_goods_option')." sgo ON sc.optionid=sgo.id WHERE sc.id=:id ", array(':id'=>$id));
    if($num !=0 && $num <= $goods_option_stock) {
        mysqld_query("update " . table('shop_cart') . " set total=$num where id=:id", array(":id" => $id));
        die(json_encode(array("code" => 1001, "message"=>"success")));
    }else{
        die(json_encode(array("code" => 1002, "message"=>"超过了")));
    }
} else if($op == 'check'){
    //2016-12-13-yanru
    $ischecked = $_GP['ischeked'];
    $id = $_GP['id'];
    $id = substr($id, 6);
    if(1 == $ischecked){
        mysqld_update('shop_cart', array('ischecked'=>1), array('id'=>$id, 'session_id'=>$openid));
    }
    if(0 == $ischecked){
        mysqld_update('shop_cart', array('ischecked'=>0), array('id'=>$id, 'session_id'=>$openid));
    }
    die(json_encode(array("result" => 1)));
    //end
}else {
    $list = mysqld_selectall("SELECT * FROM " . table('shop_cart') . " WHERE   session_id = '".$openid."'");
    $totalprice = 0;
    if (!empty($list)) {
        foreach ($list as &$item) {
//            $goods = mysqld_select("SELECT  title, thumb, marketprice, total FROM " . table('shop_goods') . " WHERE id=:id limit 1", array(":id" => $item['goodsid']));
//            $option = mysqld_select("select title,marketprice,stock from " . table("shop_goods_option") . " where id=:id limit 1", array(":id" => $item['optionid']));

            mysqld_update('shop_cart', array('ischecked'=>1), array('id'=>$item['id'], 'session_id'=>$item['session_id']));
            $goods = mysqld_select("SELECT * FROM " . table('shop_goods') . " WHERE id=:id limit 1", array(":id" => $item['goodsid']));
            $option = mysqld_select("select * from " . table("shop_goods_option") . " where id=:id limit 1", array(":id" => $item['optionid']));
            if ($option) {
                //$goods['title'] = $goods['title'];
                $goods['optionname'] = $option['title'];
                $goods['optionmarketprice'] = $option['marketprice'];
                $goods['optionstock'] = $option['stock'];
            }else{
                $goods['optionname'] = $goods['title'];
                $goods['optionmarketprice'] = $goods['marketprice'];
                $goods['optionstock'] = $goods['stock'];
            }
            $item['goods'] = $goods;
            $item['totalprice'] = (floatval($goods['optionmarketprice']) * intval($item['total']));
            $totalprice += $item['totalprice'];
        }
        unset($item);
    }
    include themePage('cart');
}