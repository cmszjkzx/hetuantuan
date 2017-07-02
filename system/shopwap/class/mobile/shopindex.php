<?php	
$settings=globaSetting();
$advs = mysqld_selectall("select * from " . table('shop_adv') . " where enabled=1  order by displayorder desc");
$allgoods = mysqld_selectcolumn("SELECT count(*) FROM " . table('shop_goods') . " WHERE deleted =0");
$children_category=array();
$category = mysqld_selectall("SELECT *,'' as list FROM " . table('shop_category') . " WHERE isrecommand=1 and enabled=1 and deleted=0 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
foreach ($category as $index => $row) {
    if (!empty($row['parentid'])) {
        $children_category[$row['parentid']][$row['id']] = $row;
        unset($category[$index]);
    }
}

$first_good_list=array();
$recommandcategory = array();
foreach ($category as &$c) {
    if ($c['isrecommand'] == 1) {
        if(!empty($_GP['kinds'])&&'0'!=$_GP['kidns']){
            //2016-11-30-yanru-begin
            //$c['list'] = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  isrecommand=1 and deleted=0 AND status = 1  and pcate='{$c['id']}' and kinds = '{$_GP['kinds']}' ORDER BY displayorder DESC, sales");
            $c['list'] = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  isrecommand=1 AND deleted=0 AND status = '1' AND pcate='{$c['id']}' AND kinds = '{$_GP['kinds']}' ORDER BY ccate, createtime DESC");
            //end
        }else {
            //2016-11-30-yanru-begin
            //$c['list'] = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  isrecommand=1 and deleted=0 AND status = 1  and pcate='{$c['id']}'  ORDER BY displayorder DESC, sales");
            $c['list'] = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  isrecommand=1 AND deleted=0 AND status = '1' AND pcate='{$c['id']}' ORDER BY ccate, createtime DESC");
            //end
        }
            foreach ($c['list'] as &$c1goods) {
            if ($c1goods['isrecommand'] == 1&&$c1goods['isfirst'] == 1) {
                $first_good_list[] = $c1goods;
            }
        }
        $recommandcategory[] = $c;
    }
    if (!empty($children_category[$c['id']])) {
        foreach ($children_category[$c['id']] as &$child) {
            if ($child['isrecommand'] == 1) {
                if(!empty($_GP['kinds'])&&'0'!=$_GP['kidns']){
                    //2016-11-30-yanru-begin
                    //$child['list'] = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  isrecommand=1 and deleted=0 AND status = 1  and pcate='{$c['id']}' and ccate='{$child['id']}' and kinds = '{$_GP['kinds']}' ORDER BY displayorder DESC, sales DESC ");
                    $child['list'] = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  isrecommand=1 AND deleted=0 AND status = '1' AND pcate='{$c['id']}' AND ccate='{$child['id']}' AND kinds = '{$_GP['kinds']}' ORDER BY ccate, createtime DESC");
                    //end
                }else {
                    //$child['list'] = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  isrecommand=1 and deleted=0 AND status = 1  and pcate='{$c['id']}' and ccate='{$child['id']}'  ORDER BY displayorder DESC, sales DESC ");
                    $child['list'] = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  isrecommand=1 AND deleted=0 AND status = '1' AND pcate='{$c['id']}' AND ccate='{$child['id']}' ORDER BY ccate, createtime DESC");
                    //end
                }foreach ($child['list'] as &$c2goods) {
                    if ($c2goods['isrecommand'] == 1&&$c2goods['isfirst'] == 1) {
                        $first_good_list[] = $c2goods;
                    }
                }
                $recommandcategory[] = $child;
            }
        }
        unset($child);
    }
}

//2016-10-26-yanru-begin
$kinds_list = mysqld_selectall("SELECT * FROM " . table('goods_kinds')." ORDER BY kinds_level ", array(), 'kinds_level');
foreach ($kinds_list as &$kinds){
    if(!empty($kinds['kinds_thumb'])){
        $thumb_path = explode("_", $kinds['kinds_thumb']);
        if(empty($_GP['kinds'])){
            $kinds['kinds_thumb'] = $thumb_path[0];
        }
        else{
            if($kinds['kinds_level'] == $_GP['kinds'])
                $kinds['kinds_thumb'] = $thumb_path[1];
            else
                $kinds['kinds_thumb'] = $thumb_path[0];
        }
    }
}
/*$has_kinds_list = mysqld_selectall("SELECT * FROM " . table('shop_goods')." WHERE  isrecommand = 1 and status = 1 and kinds != 0 ORDER BY displayorder DESC, sales DESC ");
$kinds_goods_list = array();
foreach ($kinds_list as $tlo) {
    $tplist = array();
    foreach ($has_kinds_list as $tlt) {
        if($tlo['kinds_level'] == $tlt['kinds']){
            $tplist[] = $tlt;
        }
    }
    $tlo['goods_list'] = $tplist;
    $kinds_goods_list[] = $tlo;
}
if(!empty($_GP['kinds'])&&'0'!=$_GP['kidns']){
    unset($category);
    foreach ($kinds_goods_list as $t) {
        if($_GP['kinds'] == $t['kinds_level']){
            $category = $t['goods_list'];
        }
    }
}*/
//end
/*
 * 购物车数量
 * */
$list = mysqld_selectall("SELECT * FROM " . table('shop_cart') . " WHERE   session_id = '".get_member_account(false)['openid']."'");
$cartNum=0;
foreach ($list as $t) {
    $cartNum += $t['total'];
}

$isdzd=false;
       
require(WEB_ROOT.'/system/common/extends/class/shopwap/class/mobile/shopindex_1.php');
			
if($isdzd==false) {
    include themePage('shopindex');
}

require(WEB_ROOT.'/system/common/extends/class/shopwap/class/mobile/shopindex_2.php');
			