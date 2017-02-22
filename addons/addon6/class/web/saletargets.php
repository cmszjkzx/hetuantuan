<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.hetuantuan.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: �ټ�cms <QQ:1987884799> <http://www.hetuantuan.com>
// +----------------------------------------------------------------------

$allorderprice = mysqld_selectcolumn("SELECT sum(cast(price as decimal(8,2))) FROM " . table('shop_order') . " WHERE status >= 2  ");

$allordercount = mysqld_selectcolumn("SELECT count(id) FROM " . table('shop_order') . " WHERE status >= 2  ");

$allmembercount = mysqld_selectcolumn("SELECT count(*) FROM " . table('member')." where istemplate=0");

$allorderviewcount = mysqld_selectcolumn("SELECT sum(cast(viewcount as decimal(8,0))) FROM " . table('shop_goods') );

$haveordermembercount = mysqld_selectcolumn("select count(os.openid) from (SELECT orders.openid FROM " . table('shop_order') . " orders  group by orders.openid) as os");
if(empty($allorderprice))
{
		$allorderprice=0;
}


include addons_page('saletargets');