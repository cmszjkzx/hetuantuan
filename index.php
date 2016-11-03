<?php
if(!file_exists(str_replace("\\",'/', dirname(__FILE__)).'/config/install.link'))
{
	if((empty($_REQUEST['name'])||!empty($_REQUEST['name'])&&$_REQUEST['name']!='public'))
	{
		header("location:install.php");
		exit;
	}
}
/*2016-10-14 hetuantuan change*/
/*if(defined('SYSTEM_ACT')&&SYSTEM_ACT=='mobile')
{
	$mod='mobile';
}else
{
	$mod=empty($_REQUEST['mod'])?'mobile':$_REQUEST['mod'];	
}
if($mod=='mobile')
{
	defined('SYSTEM_ACT') or define('SYSTEM_ACT', 'mobile');
}else
{
	defined('SYSTEM_ACT') or define('SYSTEM_ACT', 'index');	
}
if(empty($_REQUEST['do']))
{
	$do='shopindex';
}else
{
	$do=$_REQUEST['do'];
}*/
/*2016-10-14 hetuantuan new begin*/
if(defined('SYSTEM_ACT')&&SYSTEM_ACT=='site')//这里有个问题需要判断是否可以设置成site
{
	$mod='site';
}else
{
	$mod=empty($_REQUEST['mod'])?'site':$_REQUEST['mod'];	
}
if($mod=='site')
{
	defined('SYSTEM_ACT') or define('SYSTEM_ACT', 'index');
}else
{
	defined('SYSTEM_ACT') or define('SYSTEM_ACT', 'mobile');
}
if(empty($_REQUEST['do']))
{
	$do='index';
}else
{
	$do=$_REQUEST['do'];
}
/*end*/
if(!empty($do))
{
	ob_start();
	$CLASS_LOADER="driver";
	require 'includes/init.php';
	ob_end_flush();
	exit;
}

