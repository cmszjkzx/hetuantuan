<?php
$this->checkVersion();
$this->checkAddons();
			
$account = mysqld_select('SELECT * FROM '.table('user')." WHERE  id=:id" , array(':id'=> $_CMS[WEB_SESSION_ACCOUNT]['id']));
								
$username=	$_CMS[WEB_SESSION_ACCOUNT]['username'];
$settings=globaSetting();
$condition='';

$condition=' and `isdisable`=0 ';

$modulelist = mysqld_selectall("SELECT *,'' as menus FROM " . table('modules') . " where 1=1 $condition order by displayorder");
foreach($modulelist as $index => $module)
{
    if(checkrule($module['name'],'ALL'))
    {
        $modulelist[$index]['menus']=mysqld_selectall("SELECT * FROM " . table('modules_menu') . " WHERE `module`=:module order by id",array(':module'=>$module['name']));
					
    }else
    {
        unset($modulelist[$index]);
    }
}
//2017-1-9-yanru-begin-页面提供商家管理页面
$bands = mysqld_selectall("select distinct if(band!='', band, '其他') as band from ".table('shop_goods'));
//end
include page('main');