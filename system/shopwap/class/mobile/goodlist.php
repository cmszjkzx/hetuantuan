<?php
				
        $pindex = max(1, intval($_GP["page"]));
        $psize = 10;
        $condition = '';
        if (!empty($_GP['ccate'])) {
            $cid = intval($_GP['ccate']);
            $condition .= " AND ccate = '{$cid}'";
            $_GP['pcate'] = mysqld_selectcolumn("SELECT parentid FROM " . table('shop_category') . " WHERE id = :id", array(':id' => intval($_GP['ccate'])));
        } elseif (!empty($_GP['pcate'])) {
            $cid = intval($_GP['pcate']);
            $condition .= " AND pcate = '{$cid}'";
        }
        if (!empty($_GP['keyword'])) {
            $condition .= " AND title LIKE '%{$_GP['keyword']}%'";
        }
        $sort = empty($_GP['sort']) ? 0 : $_GP['sort'];
        $sortfield = "displayorder asc";

        $sortb0 = empty($_GP['sortb0']) ? "desc" : $_GP['sortb0'];
        $sortb1 = empty($_GP['sortb1']) ? "desc" : $_GP['sortb1'];
        $sortb2 = empty($_GP['sortb2']) ? "desc" : $_GP['sortb2'];
        $sortb3 = empty($_GP['sortb3']) ? "asc" : $_GP['sortb3'];

        if ($sort == 0) {
            $sortb00 = $sortb0 == "desc" ? "asc" : "desc";
            $sortfield = "createtime " . $sortb0;
            $sortb11 = "desc";
            $sortb22 = "desc";
            $sortb33 = "asc";
        } else if ($sort == 1) {
            $sortb11 = $sortb1 == "desc" ? "asc" : "desc";
            $sortfield = "sales " . $sortb1;
            $sortb00 = "desc";
            $sortb22 = "desc";
            $sortb33 = "asc";
        } else if ($sort == 2) {
            $sortb22 = $sortb2 == "desc" ? "asc" : "desc";
            $sortfield = "viewcount " . $sortb2;
            $sortb00 = "desc";
            $sortb11 = "desc";
            $sortb33 = "asc";
        } else if ($sort == 3) {
            $sortb33 = $sortb3 == "asc" ? "desc" : "asc";
            $sortfield = "marketprice " . $sortb3;
            $sortb00 = "desc";
            $sortb11 = "desc";
            $sortb22 = "desc";
        }

        $sorturl = mobile_url('goodlist', array("keyword" => $_GP['keyword'], "pcate" => $_GP['pcate'], "ccate" => $_GP['ccate']));
        if (!empty($_GP['isnew'])) {
            $condition .= " AND isnew = 1";
            $sorturl.="&isnew=1";
        }

        if (!empty($_GP['ishot'])) {
            $condition .= " AND ishot = 1";
            $sorturl.="&ishot=1";
        }
        if (!empty($_GP['isdiscount'])) {
            $condition .= " AND isdiscount = 1";
            $sorturl.="&isdiscount=1";
        }
        if (!empty($_GP['istime'])) {
            $condition .= " AND istime = 1 ";
            $sorturl.="&istime=1";
        }

        $children = array();



        $category = mysqld_selectall("SELECT * FROM " . table('shop_category') . " WHERE deleted=0 and enabled=1 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
        foreach ($category as $index => $row) {
            if (!empty($row['parentid'])) {
                $children[$row['parentid']][$row['id']] = $row;
                unset($category[$index]);
            }
        }
        $list = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  deleted=0 AND status = '1' $condition ORDER BY $sortfield  ");
  
        $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_goods') . " WHERE  deleted=0  AND status = '1' $condition");
        $pager = pagination($total, $pindex, $psize, $url = '', $context = array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
       

				$id = $profile['id'];
				if($profile['status']==0)
				{
					$profile['flag']=0;
				}

/*
* 购物车数量
* */
$cartlist = mysqld_selectall("SELECT * FROM " . table('shop_cart') . " WHERE   session_id = '".get_member_account(false)['openid']."'");
$cartNum=0;
foreach ($cartlist as $t) {
    $cartNum += $t['total'];
}

$isdzd=false;
				
	
        include themePage('goodlist');